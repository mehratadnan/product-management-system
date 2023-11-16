<?php

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create some sample merchants
        $merchants = [
            [
                'name' => 'adnan',
                'email' => 'adnan@example.com',
                'password' => Hash::make('adnan'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ammar',
                'email' => 'ammar@example.com',
                'password' => Hash::make('ammar'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        Merchant::insert($merchants);
    }
}
