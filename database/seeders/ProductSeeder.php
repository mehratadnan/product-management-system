<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $products = [
            [
                'merchantID' => 1,
                'productID' => 1,
                'name' => 'Product_1',
                'description' => 'This is a sample description for product 1',
                'price' => 250,
                'quantity' => 18,
                'photo_url' => 'http://example.com/images/product_1.jpg'
            ],
            [
                'merchantID' => 1,
                'productID' => 2,
                'name' => 'Product_2',
                'description' => 'This is a sample description for product 2',
                'price' => 102,
                'quantity' => 3,
                'photo_url' => 'http://example.com/images/product_2.jpg'
            ],
            [
                'merchantID' => 1,
                'productID' => 3,
                'name' => 'Product_3',
                'description' => 'This is a sample description for product 100000',
                'price' => 507,
                'quantity' => 94,
                'photo_url' => 'http://example.com/images/product_100000.jpg'
            ],
            [
                'merchantID' => 1,
                'productID' => 55,
                'name' => 'Product_55',
                'description' => 'This is a sample description for product 55',
                'price' => 342,
                'quantity' => 124,
                'photo_url' => 'http://example.com/images/product_100000.jpg'
            ]
        ];

        Product::insert($products);
    }
}
