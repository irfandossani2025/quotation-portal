<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            ['INITIAL_ADMIN_NAME', 'INITIAL_ADMIN_EMAIL', 'INITIAL_ADMIN_PHONE', 'INITIAL_ADMIN_PASSWORD', 'System Administrator', 'admin@example.com', '+968 9000 0000', 'admin', 'Muscat'],
            ['INITIAL_SALES_NAME', 'INITIAL_SALES_EMAIL', 'INITIAL_SALES_PHONE', 'INITIAL_SALES_PASSWORD', 'Muscat Sales', 'sales@example.com', '+968 9000 0001', 'preparer', 'Muscat'],
            ['INITIAL_PRICING_NAME', 'INITIAL_PRICING_EMAIL', 'INITIAL_PRICING_PHONE', 'INITIAL_PRICING_PASSWORD', 'Dubai Pricing', 'pricing@example.com', '+971 50 0000', 'pricing', 'Dubai'],
        ] as [$nameKey, $emailKey, $phoneKey, $passwordKey, $defaultName, $defaultEmail, $defaultPhone, $role, $office]) {
            $email = env($emailKey, $defaultEmail);
            User::updateOrCreate(['email' => $email], ['name' => env($nameKey, $defaultName), 'phone' => env($phoneKey, $defaultPhone),
                'role' => $role, 'office' => $office, 'password' => env($passwordKey, 'ChangeMe123!')]);
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
