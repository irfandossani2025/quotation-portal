<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_muscat_can_request_and_dubai_can_price_a_quotation(): void
    {
        $company = Company::create(['legal_name' => 'Test Co', 'trading_name' => 'Test', 'logo_path' => 'images/mais-logo.png', 'accent' => '#111111', 'quotation_prefix' => 'TQ', 'currency' => 'OMR', 'vat_rate' => 5]);
        $product = Product::create(['supplier' => 'MTC', 'source_key' => 'p1', 'source_url' => 'https://example.test/p1', 'name' => 'Bottle']);
        $sales = User::factory()->create(['role' => 'preparer', 'office' => 'Muscat']);
        $pricing = User::factory()->create(['role' => 'pricing', 'office' => 'Dubai']);

        $this->actingAs($sales)->post(route('quotations.store'), [
            'company_id' => $company->id, 'customer_name' => 'Acme', 'valid_until' => now()->addMonth()->toDateString(),
            'product_id' => [$product->id], 'quantity' => [$product->id => 10],
        ])->assertRedirect();

        $quotation = Quotation::firstOrFail();
        $this->assertSame('pricing', $quotation->status);
        $item = $quotation->items()->firstOrFail();
        $this->actingAs($pricing)->get(route('pricing.index'))
            ->assertOk()
            ->assertSee('Test')
            ->assertSee(route('pricing.edit', $quotation));
        $this->actingAs($pricing)->put(route('pricing.update', $quotation), ['prices' => [$item->id => 2.125]])->assertRedirect(route('pricing.index'));

        $quotation->refresh();
        $this->assertSame('priced', $quotation->status);
        $this->assertSame('21.250', $quotation->subtotal);
        $this->assertSame('1.063', $quotation->vat_amount);
        $this->assertSame('22.313', $quotation->total);
    }

    public function test_pricing_user_is_sent_to_pricing_after_login(): void
    {
        $pricing = User::factory()->create(['role' => 'pricing', 'office' => 'Dubai', 'password' => 'password']);

        $this->from(route('dashboard'))->post(route('login'), [
            'email' => $pricing->email, 'password' => 'password',
        ])->assertRedirect(route('pricing.index'));
    }
}
