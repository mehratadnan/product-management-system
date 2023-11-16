<?php
namespace Tests\Commands;

use Database\Factories\ImportRequestFactory;
use Database\Factories\ProductFactory;
use Database\Factories\MerchantFactory;
use App\Console\Commands\ImportProductsCommand;
use App\Models\ImportRequest;
use App\Models\History;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportProductsTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @test
    */
    public function testImportsProductsUpdateDeleteInsertTest()
    {
        $ref = ImportRequest::createRef();
        $importRequest = ImportRequestFactory::new()->create([
            'merchantID' => 1,
            'type' => 'xml',
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => base_path('tests/Commands/TestFiles/testProducts.xml'),
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $merchant = MerchantFactory::new()->create(['id' => $importRequest->merchant_id]);

        $product1 = ProductFactory::new()->create([
            'id' => 1,
            'quantity' => 1,
            'price' => 1,
            'merchantID' => 1,
            'name' => "product_1",
            'description' => "product_1",
            'productID' => 1,
            'status' => Product::PRODUCT_STATUS_SALE,
            'photo_url' => "",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product2 = ProductFactory::new()->create([
            'id' => 2,
            'quantity' => 1,
            'price' => 1,
            'merchantID' => 1,
            'name' => "product_1",
            'description' => "product_1",
            'productID' => 10,
            'status' => Product::PRODUCT_STATUS_SALE,
            'photo_url' => "",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->artisan('import:products')->assertExitCode(0);
        
        $this->assertDatabaseCount('products', 4);
        $this->assertDatabaseHas('products', [
            'productID' => $product2['productID'],
            'status' => Product::PRODUCT_STATUS_DELETED,
        ]);
        $this->assertDatabaseHas('products', [
            'productID' => $product1['productID'],
            'quantity' => 18,
            'price' => 250,
            'status' => Product::PRODUCT_STATUS_SALE,
        ]);
        $this->assertDatabaseHas('import_requests', [
            'ref' => $ref,
            'status' => ImportRequest::STATUS_SUCCESS,
            'message' => null,
        ]);
    }

    /**
    * @test
    */
    public function testImportsProductsInsert()
    {
        $ref = ImportRequest::createRef();
        $importRequest = ImportRequestFactory::new()->create([
            'merchantID' => 1,
            'type' => 'xml',
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => base_path('tests/Commands/TestFiles/testProducts.xml'),
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $merchant = MerchantFactory::new()->create(['id' => $importRequest->merchant_id]);

        $this->artisan('import:products')->assertExitCode(0);

        $this->assertDatabaseCount('products', 3);
        $this->assertDatabaseHas('import_requests', [
            'ref' => $ref,
            'status' => ImportRequest::STATUS_SUCCESS,
            'message' => null,
        ]);
    }


    /**
    * @test
    */
    public function testImportsProductsEmptyFile()
    {
        $ref = ImportRequest::createRef();
        $importRequest = ImportRequestFactory::new()->create([
            'merchantID' => 1,
            'type' => 'xml',
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => base_path('tests/Commands/TestFiles/testProducts.xml'),
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $merchant = MerchantFactory::new()->create(['id' => $importRequest->merchant_id]);

        $this->artisan('import:products')->assertExitCode(0);
    }

    /**
    * @test
    */
    public function testImportsProductsUpdateDeleteInsertForMultipleMerchants()
    {
        $merchant1 = MerchantFactory::new()->create(['id' => 1, 'email'=>"adnan1@ex.com" ]);
        $merchant2 = MerchantFactory::new()->create(['id' => 2, 'email'=>"adnan2@ex.com"]);

        $ref1 = ImportRequest::createRef();
        $importRequest1 = ImportRequestFactory::new()->create([
            'id' => 1,
            'merchantID' => $merchant1['id'],
            'type' => 'xml',
            'ref' => $ref1,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => base_path('tests/Commands/TestFiles/testProducts.xml'),
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ref2 = ImportRequest::createRef();
        $importRequest2 = ImportRequestFactory::new()->create([
            'id' => 2,
            'merchantID' => $merchant1['id'],
            'type' => 'xml',
            'ref' => $ref2,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => base_path('tests/Commands/TestFiles/testProducts.xml'),
            'status' => ImportRequest::STATUS_ERROR,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ref3 = ImportRequest::createRef();
        $importRequest3 = ImportRequestFactory::new()->create([
            'id' => 3,
            'merchantID' => $merchant2['id'],
            'type' => 'xml',
            'ref' => $ref3,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => base_path('tests/Commands/TestFiles/testProducts.xml'),
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product1 = ProductFactory::new()->create([
            'id' => 1,
            'quantity' => 1,
            'price' => 1,
            'merchantID' => $merchant1['id'],
            'name' => "product_1",
            'description' => "product_1",
            'productID' => 1,
            'status' => Product::PRODUCT_STATUS_SALE,
            'photo_url' => "",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product2 = ProductFactory::new()->create([
            'id' => 2,
            'quantity' => 1,
            'price' => 1,
            'merchantID' => $merchant1['id'],
            'name' => "product_2",
            'description' => "product_2",
            'productID' => 10,
            'status' => Product::PRODUCT_STATUS_SALE,
            'photo_url' => "",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $product3 = ProductFactory::new()->create([
            'id' => 3,
            'quantity' => 1,
            'price' => 1,
            'merchantID' => $merchant2['id'],
            'name' => "product_1",
            'description' => "product_1",
            'productID' => 2,
            'status' => Product::PRODUCT_STATUS_SALE,
            'photo_url' => "",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->artisan('import:products')->assertExitCode(0);
        
        $this->assertDatabaseCount('products', 7);
        $this->assertDatabaseHas('products', [
            'merchantID' => $merchant1['id'],
            'productID' => $product2['productID'],
            'status' => Product::PRODUCT_STATUS_DELETED,
        ]);
        $this->assertDatabaseHas('products', [
            'merchantID' => $merchant1['id'],
            'productID' => $product1['productID'],
            'quantity' => 18,
            'price' => 250,
            'status' => Product::PRODUCT_STATUS_SALE,
        ]);
        $this->assertDatabaseHas('products', [
            'merchantID' => $merchant1['id'],
            'productID' => 2,
            'status' => Product::PRODUCT_STATUS_SALE,
        ]);
        $this->assertDatabaseHas('products', [
            'merchantID' => $merchant1['id'],
            'productID' => 3,
            'status' => Product::PRODUCT_STATUS_SALE,
        ]);
        $this->assertDatabaseHas('products', [
            'merchantID' => $merchant2['id'],
            'productID' => 1,
            'status' => Product::PRODUCT_STATUS_SALE,
        ]);
        $this->assertDatabaseHas('products', [
            'merchantID' => $merchant2['id'],
            'productID' => $product3['productID'],
            'quantity' => 3,
            'price' => 102,
            'status' => Product::PRODUCT_STATUS_SALE,
        ]);
        $this->assertDatabaseHas('products', [
            'merchantID' => $merchant2['id'],
            'productID' => 3,
            'status' => Product::PRODUCT_STATUS_SALE,
        ]);
        $this->assertDatabaseHas('import_requests', [
            'ref' => $ref1,
            'status' => ImportRequest::STATUS_SUCCESS,
            'message' => null,
        ]);
        $this->assertDatabaseHas('import_requests', [
            'ref' => $ref2,
            'status' => ImportRequest::STATUS_ERROR,
            'message' => null,
        ]);
        $this->assertDatabaseHas('import_requests', [
            'ref' => $ref3,
            'status' => ImportRequest::STATUS_SUCCESS,
            'message' => null,
        ]);
    }

    /**
    * @test
    */
    public function testImportsProductsProccessGetDataError()
    {
        $ref = ImportRequest::createRef();
        $importRequest = ImportRequestFactory::new()->create([
            'merchantID' => 1,
            'type' => 'xml',
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => base_path('tests/Commands/TestFiles/testProducts.xml'),
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $merchant = MerchantFactory::new()->create(['id' => $importRequest->merchant_id]);

        $importProductsCommandMock = $this->getMockBuilder(ImportProductsCommand::class)
            ->onlyMethods(['getData'])
            ->getMock();
            
        $importProductsCommandMock->method('getData')->willReturnCallback(function () {
            return ['success' => false, 'message' => 'getData Error'];
        });

        $result = $importProductsCommandMock->handle();

        $this->assertSame(0, $result);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseHas('import_requests', [
            'ref' => $ref,
            'status' => ImportRequest::STATUS_ERROR,
            'message' => "getData Error",
        ]);
    }
    /**
        * @test
        */
    public function testImportsProductsProccessGetDataExceptionTest()
    {
        $ref = ImportRequest::createRef();
        $importRequest = ImportRequestFactory::new()->create([
            'merchantID' => 1,
            'type' => 'xml',
            'ref' => $ref,
            'pathType' => ImportRequest::PATH_LOCAL,
            'path' => base_path('tests/Commands/TestFiles/testProducts.xml'),
            'status' => ImportRequest::STATUS_NEW,
            'message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $merchant = MerchantFactory::new()->create(['id' => $importRequest->merchant_id]);

        $importProductsCommandMock = $this->getMockBuilder(ImportProductsCommand::class)
            ->onlyMethods(['getData'])
            ->getMock();
            
        $importProductsCommandMock->method('getData')->willReturnCallback(function () {
            throw new \Exception('getData Error');
        });

        $result = $importProductsCommandMock->handle();

        $this->assertSame(1, $result);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseHas('import_requests', [
            'ref' => $ref,
            'status' => ImportRequest::STATUS_ERROR,
            'message' => "getData Error",
        ]);
        $this->assertDatabaseHas('history', [
            "class" => "App\\Console\\Commands\\ImportProductsCommand",
            "method" => "App\\Console\\Commands\\ImportProductsCommand::handle",
            "message" => "getData Error",
        ]);
    }

    /**
     * @test
     */
    public function testImportsProductsNoRecordsToProccessTest()
    {
        $importRequest = $this->getMockBuilder(ImportRequest::class)
            ->onlyMethods(['listNewImportRequest'])
            ->getMock();

        $importRequest->method('listNewImportRequest')->willReturnCallback(function () {
            return [
                'success' => true,
                'data' => []
            ];
        });

        $importProductsCommandMock = $this->getMockBuilder(ImportProductsCommand::class)
            ->onlyMethods(['getData'])
            ->getMock();

        $importProductsCommandMock->importRequest = $importRequest;

        $result = $importProductsCommandMock->handle();
        $this->assertSame(0, $result);
    }
    
}
