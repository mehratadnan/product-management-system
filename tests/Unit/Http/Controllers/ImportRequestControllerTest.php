<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ImportRequestController;
use App\Models\ImportRequest;
use Illuminate\Http\Request;
use Database\Factories\ImportRequestFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @test
    */
    public function testCreateImportRequestWithValidData(){

        $response = $this->post('/import-request/add', [
            'file_path' => 'https://example.com/file.xml',
            'merchant_id' => 1,
        ]);

        $this->assertTrue($response->getData()->success);

        $this->assertDatabaseHas('import_requests', [
            'status' => ImportRequest::STATUS_NEW,
            'path' => 'https://example.com/file.xml',
            'merchantID' => 1,
        ]);
    }

    /**
    * @test
    */
    public function testCreateImportRequestWithInValidData(){

        $response = $this->post('/import-request/add', [
            'file_path' => 'https://example.com/file.xml',
        ]);

        $this->assertFalse($response->getData()->success);
        $this->assertEquals($response->getData()->message, "Missing Required Fields");
        $this->assertDatabaseCount('import_requests', 0);
    }

    /**
    * @test
    */
    public function testUpdateImportRequestWithValidData(){
        $ref = ImportRequest::createRef();
        $importRequest = ImportRequestFactory::new()->create([
            'merchantID' => 1,
            'type' => 'xml',
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => __DIR__ .'\TestFiles\testProducts.xml',
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->put('/import-request/update', [
            "ref" => $ref,
            'status' => ImportRequest::STATUS_ERROR
        ]);

        $this->assertTrue($response->getData()->success);
        $this->assertDatabaseCount('import_requests', 1);
        $this->assertDatabaseHas('import_requests', [
            "ref" => $ref,
            'status' => ImportRequest::STATUS_ERROR,
            'path' => __DIR__ .'\TestFiles\testProducts.xml',
        ]);
    }

     /**
    * @test
    */
    public function testUpdateImportRequestWithInValidData(){
        $ref = ImportRequest::createRef();
        $importRequest = ImportRequestFactory::new()->create([
            'merchantID' => 1,
            'type' => 'xml',
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => __DIR__ .'\TestFiles\testProducts.xml',
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->put('/import-request/update', [
            "file_path" => "https://www.zzgtech.com/demo_data/products_2022_06_02.xml",
            'status' => ImportRequest::STATUS_NEW
        ]);

        $this->assertFalse($response->getData()->success);
        $this->assertEquals($response->getData()->message, "Missing Required Fields");
        $this->assertDatabaseCount('import_requests', 1);
        $this->assertDatabaseHas('import_requests', [
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => __DIR__ .'\TestFiles\testProducts.xml',
            'status' => ImportRequest::STATUS_NEW,
        ]);
    }

    /**
    * @test
    */
    public function testDeleteImportRequestWithValidData(){
        $ref = ImportRequest::createRef();
        $importRequest = ImportRequestFactory::new()->create([
            'merchantID' => 1,
            'type' => 'xml',
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => __DIR__ .'\TestFiles\testProducts.xml',
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->delete('/import-request/delete', [
            "ref" => $ref,
        ]);

        $this->assertTrue($response->getData()->success);
        $this->assertDatabaseCount('import_requests', 0);
    }

    /**
    * @test
    */
    public function testDeleteImportRequestWithInValidData(){
        $ref = ImportRequest::createRef();
        $importRequest = ImportRequestFactory::new()->create([
            'merchantID' => 1,
            'type' => 'xml',
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => __DIR__ .'\TestFiles\testProducts.xml',
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->delete('/import-request/delete', [
            "ref" => "asdasd",
        ]);

        $this->assertFalse($response->getData()->success);
        $this->assertEquals($response->getData()->message, 'No Record Found');
        $this->assertDatabaseCount('import_requests', 1);
        $this->assertDatabaseHas('import_requests', [
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => __DIR__ .'\TestFiles\testProducts.xml',
            'status' => ImportRequest::STATUS_NEW,
        ]);
    }
}
