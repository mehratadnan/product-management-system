<?php

namespace Database\Seeders;

use App\Models\ImportRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ImportRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create some sample importRequests
        $importRequests = [
            [
                'merchantID' => '1',
                'type' => 'xml',
                'ref' => "request1_9xdhSzbXk6",
                'pathType' => ImportRequest::PATH_LOCAL,
                'path' => base_path('tests/Commands/TestFiles/newtestProducts.xml'),
                'status' => 'new',
                'message' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'merchantID' => '2',
                'type' => 'xml',
                'ref' => "request2_9xdhSzbXk6",
                'pathType' => ImportRequest::PATH_LOCAL,
                'path' => base_path('tests/Commands/TestFiles/newtestProducts.xml'),
                'status' => 'new',
                'message' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        ImportRequest::insert($importRequests);
    }
}
