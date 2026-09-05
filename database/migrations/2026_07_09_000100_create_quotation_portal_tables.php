<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('legal_name');
            $table->string('trading_name');
            $table->string('logo_path');
            $table->string('accent', 20);
            $table->string('quotation_prefix', 20);
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('vat_number')->nullable();
            $table->decimal('vat_rate', 5, 2)->default(5);
            $table->string('currency', 3)->default('OMR');
            $table->text('terms')->nullable();
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('supplier');
            $table->string('source_key');
            $table->text('source_url');
            $table->string('sku')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();
            $table->string('category')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('source_updated_at')->nullable();
            $table->timestamps();
            $table->unique(['supplier', 'source_key']);
            $table->index(['supplier', 'name']);
        });
        Schema::create('quotation_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('next_number')->default(1);
            $table->timestamps();
            $table->unique(['company_id', 'year']);
        });
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->string('number')->unique();
            $table->string('customer_name');
            $table->string('customer_company')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('subject')->nullable();
            $table->date('quotation_date');
            $table->date('valid_until');
            $table->enum('status', ['draft', 'pricing', 'priced', 'issued'])->default('draft');
            $table->decimal('subtotal', 14, 3)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(5);
            $table->decimal('vat_amount', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('sort_order');
            $table->text('description');
            $table->text('photo_url')->nullable();
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_price', 14, 3)->nullable();
            $table->decimal('line_total', 14, 3)->default(0);
            $table->foreignId('priced_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('priced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('quotation_sequences');
        Schema::dropIfExists('products');
        Schema::dropIfExists('companies');
    }
};
