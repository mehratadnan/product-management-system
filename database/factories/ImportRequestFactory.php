<?php

namespace Database\Factories;

use App\Models\ImportRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImportRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ImportRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => 1,
            'merchantID' => '1',
            'type' => 'xml',
            'ref' => ImportRequest::createRef(),
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => 'https://www.zzgtech.com/demo_data/products_2022_06_01.xml',
            'status' => 'new',
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}