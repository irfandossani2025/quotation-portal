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
        Company::create(['legal_name' => 'Lustrous Glory International', 'trading_name' => 'Mais / Hadaya Muscat',
            'logo_path' => 'images/mais-logo.png', 'accent' => '#d51f2c', 'quotation_prefix' => 'LGI', 'address' => 'Muscat, Sultanate of Oman',
            'currency' => 'OMR', 'vat_rate' => 5, 'terms' => 'Validity and delivery terms are as stated in this quotation.']);
        Company::create(['legal_name' => 'Mugdi Investments LLC', 'trading_name' => 'Mudgi Investments',
            'logo_path' => 'images/mudgi-logo.png', 'accent' => '#42b5aa', 'quotation_prefix' => 'MI', 'address' => 'Muscat, Sultanate of Oman',
            'currency' => 'OMR', 'vat_rate' => 5, 'terms' => 'Validity and delivery terms are as stated in this quotation.']);

        User::create(['name' => 'System Administrator', 'email' => 'admin@example.com', 'phone' => '+968 9000 0000', 'role' => 'admin', 'office' => 'Muscat', 'password' => 'ChangeMe123!']);
        User::create(['name' => 'Muscat Sales', 'email' => 'sales@example.com', 'phone' => '+968 9000 0001', 'role' => 'preparer', 'office' => 'Muscat', 'password' => 'ChangeMe123!']);
        User::create(['name' => 'Dubai Pricing', 'email' => 'pricing@example.com', 'phone' => '+971 50 000 0000', 'role' => 'pricing', 'office' => 'Dubai', 'password' => 'ChangeMe123!']);

        foreach ([
            ['MTC', 'MTC-POWER-01', 'Eco-friendly wireless power bank', 'Technology Gifts', 'https://mtc.ae/'],
            ['Luxury Trading', 'LUX-GIFT-01', 'Premium executive gift set', 'Gift Sets', 'https://luxurytrd.com/'],
            ['Jasani', 'JAS-BOTTLE-01', 'Insulated reusable bottle', 'Drinkware', 'https://www.jasani.ae/'],
            ['HAK+', 'HAK-NOTE-01', 'A5 recycled notebook', 'Stationery', 'https://www.hakplus.com/'],
        ] as [$supplier, $key, $name, $category, $url]) {
            Product::create(['supplier' => $supplier, 'source_key' => $key, 'source_url' => $url, 'sku' => $key,
                'name' => $name, 'description' => $name, 'category' => $category]);
        }
    }
}
