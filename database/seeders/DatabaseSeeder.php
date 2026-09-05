<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Company::updateOrCreate(['legal_name' => 'Lustrous Glory International'], ['trading_name' => 'Mais / Hadaya Muscat',
            'logo_path' => 'images/mais-logo.png', 'accent' => '#d51f2c', 'quotation_prefix' => 'LGI', 'address' => 'Muscat, Sultanate of Oman',
            'currency' => 'OMR', 'vat_rate' => 5, 'terms' => 'Validity and delivery terms are as stated in this quotation.']);
        Company::updateOrCreate(['legal_name' => 'Mugdi Investments LLC'], ['trading_name' => 'Mudgi Investments',
            'logo_path' => 'images/mudgi-logo.png', 'accent' => '#42b5aa', 'quotation_prefix' => 'MI', 'address' => 'Muscat, Sultanate of Oman',
            'currency' => 'OMR', 'vat_rate' => 5, 'terms' => 'Validity and delivery terms are as stated in this quotation.']);

        foreach ([
            ['admin', 'admin', 'Muscat'],
            ['sales', 'preparer', 'Muscat'],
            ['pricing', 'pricing', 'Dubai'],
        ] as [$key, $role, $office]) {
            $user = config("initial_users.$key");
            if (blank($user['email']) || blank($user['password'])) {
                throw new RuntimeException('Set INITIAL_'.strtoupper($key).'_EMAIL and INITIAL_'.strtoupper($key).'_PASSWORD before seeding production users.');
            }
            User::updateOrCreate(['email' => $user['email']], ['name' => $user['name'], 'phone' => $user['phone'],
                'role' => $role, 'office' => $office, 'password' => $user['password']]);
        }

        foreach ([
            ['MTC', 'MTC-POWER-01', 'Eco-friendly wireless power bank', 'Technology Gifts', 'https://mtc.ae/'],
            ['Luxury Trading', 'LUX-GIFT-01', 'Premium executive gift set', 'Gift Sets', 'https://luxurytrd.com/'],
            ['Jasani', 'JAS-BOTTLE-01', 'Insulated reusable bottle', 'Drinkware', 'https://www.jasani.ae/'],
            ['HAK+', 'HAK-NOTE-01', 'A5 recycled notebook', 'Stationery', 'https://www.hakplus.com/'],
        ] as [$supplier, $key, $name, $category, $url]) {
            Product::updateOrCreate(['supplier' => $supplier, 'source_key' => $key], ['source_url' => $url, 'sku' => $key,
                'name' => $name, 'description' => $name, 'category' => $category]);
        }
    }
}
