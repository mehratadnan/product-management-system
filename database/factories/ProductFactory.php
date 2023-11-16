<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => 1,
            'quantity' => 1,
            'price' => 1,
            'merchantID' => 1,
            'name' => "product_1",
            'description' => "product_1",
            'productID' => 1,
            'status' => 'sale',
            'photo_url' => "",
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}