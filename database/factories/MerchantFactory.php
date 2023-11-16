<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Hash;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MerchantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Merchant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => 1,
            'name' => 'adnan',
            'email' => 'adnan@example.com',
            'password' => Hash::make('adnan'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}