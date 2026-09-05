<?php

namespace App\Console\Commands;

use App\Models\Product;
use DOMDocument;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

class ImportSupplierCatalog extends Command
{
    protected $signature = 'catalog:import {source? : mtc, luxury, jasani, or hak} {--limit=0 : Maximum products per source; 0 means all}';

    protected $description = 'Synchronize public product metadata from approved supplier websites';

    private array $sources = [
        'mtc' => ['name' => 'MTC', 'base' => 'https://mtc.ae', 'maps' => ['/wp-sitemap.xml', '/sitemap_index.xml']],
        'luxury' => ['name' => 'Luxury Trading', 'base' => 'https://luxurytrd.com', 'maps' => ['/wp-sitemap.xml', '/sitemap_index.xml', '/sitemap.xml']],
        'jasani' => ['name' => 'Jasani', 'base' => 'https://www.jasani.ae', 'maps' => ['/sitemap.xml']],
        'hak' => ['name' => 'HAK+', 'base' => 'https://www.hakplus.com', 'maps' => ['/sitemap_index.xml', '/wp-sitemap.xml', '/sitemap.xml']],
    ];

    public function handle(): int
    {
        $selected = $this->argument('source');
        if ($selected && ! isset($this->sources[$selected])) {
            $this->error('Unknown source. Use mtc, luxury, jasani, or hak.');

            return self::FAILURE;
        }
        $sources = $selected ? [$selected => $this->sources[$selected]] : $this->sources;
        foreach ($sources as $key => $source) {
            $this->importSource($key, $source);
        }

        return self::SUCCESS;
    }

    private function importSource(string $key, array $source): void
    {
        $this->newLine();
        $this->info("Discovering {$source['name']} products...");
        $urls = [];
        $limit = (int) $this->option('limit');
        foreach ($source['maps'] as $map) {
            try {
                $urls = array_merge($urls, $this->readSitemap($source['base'].$map, $source['base'], 0, $limit > 0 ? max(25, $limit * 4) : 0));
                if ($urls) {
                    break;
                }
            } catch (Throwable $e) {
                $this->warn("Could not read {$map}; trying the next public sitemap.");
            }
        }
        $urls = array_values(array_unique(array_filter($urls, fn ($url) => $this->looksLikeProduct($url))));
        if ($limit > 0) {
            $urls = array_slice($urls, 0, $limit);
        }
        $saved = 0;
        $bar = $this->output->createProgressBar(count($urls));
        $bar->start();
        foreach ($urls as $url) {
            try {
                if ($this->importProduct($source['name'], $url)) {
                    $saved++;
                }
            } catch (Throwable $e) { /* A changed or protected supplier page is skipped safely. */
            }
            $bar->advance();
            usleep(200000);
        }
        $bar->finish();
        $this->newLine();
        $this->info("Saved or updated {$saved} {$source['name']} products.");
    }

    private function readSitemap(string $url, string $base, int $depth, int $max): array
    {
        if ($depth > 2) {
            return [];
        }
        $response = Http::timeout(25)->retry(2, 500)->withHeaders(['User-Agent' => 'QuotationCatalogSync/1.0 (internal product catalog)'])->get($url);
        $response->throw();
        $xml = new SimpleXMLElement($response->body());
        $urls = [];
        if ($xml->getName() === 'sitemapindex') {
            $maps = [];
            foreach ($xml->sitemap as $map) {
                $maps[] = (string) $map->loc;
            }
            usort($maps, fn ($a, $b) => (int) ! str_contains($a, 'product') <=> (int) ! str_contains($b, 'product'));
            foreach ($maps as $location) {
                if (str_starts_with($location, $base)) {
                    $urls = array_merge($urls, $this->readSitemap($location, $base, $depth + 1, $max));
                }
                if ($max > 0 && count($urls) >= $max) {
                    break;
                }
            }
        } else {
            foreach ($xml->url as $item) {
                $urls[] = (string) $item->loc;
            }
        }

        return $urls;
    }

    private function looksLikeProduct(string $url): bool
    {
        $path = strtolower((string) parse_url($url, PHP_URL_PATH));

        return str_contains($path, '/product/') || str_contains($path, '/shop/') || str_contains($path, '/products/');
    }

    private function importProduct(string $supplier, string $url): bool
    {
        $html = Http::timeout(20)->retry(2, 400)->withHeaders(['User-Agent' => 'QuotationCatalogSync/1.0 (internal product catalog)'])->get($url)->throw()->body();
        libxml_use_internal_errors(true);
        $doc = new DOMDocument;
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $jsonProduct = null;
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $node) {
            $data = json_decode($node->textContent, true);
            foreach ($this->flattenJsonLd($data) as $entry) {
                if (($entry['@type'] ?? null) === 'Product') {
                    $jsonProduct = $entry;
                    break 2;
                }
            }
        }
        $meta = function (string $property) use ($xpath) {
            $node = $xpath->query("//meta[@property='$property' or @name='$property']/@content")->item(0);

            return $node ? trim($node->nodeValue) : null;
        };
        $name = $jsonProduct['name'] ?? $meta('og:title');
        if (! $name) {
            return false;
        }
        $description = $jsonProduct['description'] ?? $meta('og:description');
        $image = $jsonProduct['image'] ?? $meta('og:image');
        if (is_array($image)) {
            $image = is_string($image[0] ?? null) ? $image[0] : ($image[0]['url'] ?? null);
        }
        $sku = $jsonProduct['sku'] ?? null;
        $sourceKey = $sku ?: sha1(Str::lower(rtrim($url, '/')));
        Product::updateOrCreate(['supplier' => $supplier, 'source_key' => $sourceKey], [
            'source_url' => $url, 'sku' => $sku, 'name' => Str::limit(strip_tags($name), 255, ''),
            'description' => $description ? strip_tags($description) : null, 'image_url' => $image,
            'category' => null, 'active' => true, 'source_updated_at' => now(),
        ]);

        return true;
    }

    private function flattenJsonLd(mixed $data): array
    {
        if (! is_array($data)) {
            return [];
        }
        if (isset($data['@graph']) && is_array($data['@graph'])) {
            return $data['@graph'];
        }

        return array_is_list($data) ? $data : [$data];
    }
}
