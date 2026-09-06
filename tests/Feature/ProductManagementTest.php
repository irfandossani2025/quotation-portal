<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_a_product_with_an_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('products.store'), [
            'supplier' => 'MTC', 'sku' => 'MTC-001', 'name' => 'Travel mug',
            'description' => 'Insulated stainless steel mug.', 'category' => 'Drinkware',
            'image' => UploadedFile::fake()->image('travel-mug.jpg'),
        ])->assertRedirect()->assertSessionHas('success');

        $product = Product::firstOrFail();
        $this->assertSame('Travel mug', $product->name);
        $this->assertStringContainsString('/storage/products/', $product->image_url);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', parse_url($product->image_url, PHP_URL_PATH)));
    }

    public function test_non_admin_cannot_manage_products(): void
    {
        $user = User::factory()->create(['role' => 'preparer']);

        $this->actingAs($user)->get(route('products.index'))->assertForbidden();
        $this->actingAs($user)->post(route('products.store'), [
            'supplier' => 'MTC', 'name' => 'Travel mug',
        ])->assertForbidden();
    }
}