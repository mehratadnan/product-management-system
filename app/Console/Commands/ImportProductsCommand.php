<?php

namespace App\Console\Commands;

use App\Http\Helpers\FileProvider;
use App\Models\ImportRequest;
use App\Models\Merchant;
use App\Models\History;
use App\Models\Product;
use App\RequestType\CollectRequestData;
use Exception;
use Illuminate\Console\Command;

class ImportProductsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'import:products';

    /**
     * @var string
     */
    protected $description = 'Imports products into database';
    /**
     * @var Product
     */
    private $product;
    /**
     * @var Merchant
     */
    public $merchant;
    /**
     * @var ImportRequest
     */
    public $importRequest;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->product = $this->product ?? new Product();
        $this->merchant = $this->merchant ?? new Merchant();
        $this->importRequest = $this->importRequest ?? new ImportRequest();
        parent::__construct();
    }

    public function handle()
    {
        ini_set('memory_limit', '512M');

        try {
            $importRequests = $this->importRequest->listNewImportRequest();
            if(empty($importRequests['success'])){
                return 0;
            }

            if(empty($importRequests['data'])){
                $this->write("No Records to Proccess.");
                return 0;
            }

            $count = count($importRequests['data']);
            $this->write("Number of Selected Requests is $count.");

            foreach ($importRequests['data'] as $key => $importRequest){
                $merchantID = $importRequest->getMerchantID();

                $this->write("\n ".($key+1)."/$count "."Process Import Request (".$importRequest->getRef().") For Merchant $merchantID");

                $this->importRequest->updateImportRequest(
                    $importRequest->getRef(),
                    ImportRequest::STATUS_PROCESSING
                );

                /** @var ImportRequest $importRequest */
                $result = $this->getData($importRequest);
                if(empty($result['success'])){
                    $this->write($result['message'],false);
                    $this->importRequest->updateImportRequest(
                        $importRequest->getRef(),
                        ImportRequest::STATUS_ERROR,
                        $result['message']
                    );
                    continue;
                }

                $importableData = $result['data']['importable_data'];
                if(empty($importableData)){
                    $this->write("No record to Insert.",false);
                    $this->importRequest->updateImportRequest(
                        $importRequest->getRef(),
                        ImportRequest::STATUS_ERROR,
                        'No Record to Insert'
                    );
                    continue;
                }

                $this->write(($key+1)."/$count ".count($importableData)." Products Were Found");

                $corruptData = $result['data']['corrupt_data'];
                if(!empty($corruptData)){
                    $this->write(($key+1)."/$count "."Corrupt Data Found = ".count($corruptData));
                }

                $result = $this->product->getInsertedProductIDs($merchantID);
                if(empty($result['success'])){
                    $this->write($result['message'],false);
                    $this->importRequest->updateImportRequest(
                        $importRequest->getRef(),
                        ImportRequest::STATUS_ERROR,
                        $result['message']
                    );
                    continue;
                }

                $productIDs = $result['data'];
                $result = $this->product->compareProductArrays($importableData, $productIDs);
                if(empty($result['success'])){
                    $this->write($result['message'],false);
                    $this->importRequest->updateImportRequest(
                        $importRequest->getRef(),
                        ImportRequest::STATUS_ERROR,
                        $result['message']
                    );
                    continue;
                }

                $productsToInsert = $result['data']['productsToInsert'] ?? [];
                $insertionResult = $this->product->insertMultipleProducts($productsToInsert);
                if(empty($insertionResult['success']) && !empty($productsToInsert)){
                    $this->write('Insertion Fail => '.$insertionResult['message'],false);
                    $this->importRequest->updateImportRequest(
                        $importRequest->getRef(),
                        ImportRequest::STATUS_ERROR,
                        $insertionResult['message']
                    );
                    continue;
                }else{
                    $this->write(($key+1)."/$count ".count($productsToInsert)." Products Were Inserted");
                }


                $productsToDelete = $result['data']['productsToDelete'] ?? [];
                $deletionResult = $this->product->deleteProducts($productsToDelete, $merchantID);
                if(empty($deletionResult['success']) && !empty($productsToDelete)){
                    $this->write('Deletion Fail: => '.$deletionResult['message'],false);
                    $this->importRequest->updateImportRequest(
                        $importRequest->getRef(),
                        ImportRequest::STATUS_ERROR,
                        $deletionResult['message']
                    );
                    continue;
                }else{
                    $this->write(($key+1)."/$count ".count($productsToDelete)." Products Were Deleted");
                }

                $productsToUpdate = $result['data']['productsToUpdate'] ?? [];
                $updationResult = $this->product->updateProducts($productsToUpdate, $merchantID);
                if(empty($updationResult['success']) && !empty($productsToUpdate)){
                    $this->write('Updation Fail: => '.$updationResult['message'],false);
                    $this->importRequest->updateImportRequest(
                        $importRequest->getRef(),
                        ImportRequest::STATUS_ERROR,
                        $updationResult['message']
                    );
                    continue;
                }else{
                    $this->write(($key+1)."/$count ".count($productsToUpdate)." Products Were Updated");
                }


                $this->importRequest->updateImportRequest(
                    $importRequest->getRef(),
                    ImportRequest::STATUS_SUCCESS
                );

                $this->write("Process Import Request For Merchant $merchantID Done", true);
            }

            return 0;
        }catch (\Exception $e){
            $this->write($e->getMessage(),false);
            $this->importRequest->updateImportRequest(
                $importRequest->getRef(),
                ImportRequest::STATUS_ERROR,
                $e->getMessage()
            );
            (new History())
                ->setClass(__CLASS__)
                ->setMethod(__METHOD__)
                ->setRef($e->getLine())
                ->setMessage($e->getMessage())
                ->save();
            return 1;
        }
    }

    /**
     * @param ImportRequest $importRequest
     * @return array|CollectRequestData
     */
    public function getData(ImportRequest $importRequest): array
    {
        return (new CollectRequestData())->collectData($importRequest);
    }

    public function write(string $message, ?bool $success = null){
        if (!app()->environment('testing')) {
            $type = "";
            if($success !== null){
                $type = ($success === true) ? " Success" : " Error";
                $type = $type." =>";
            }
            fwrite(STDERR, $type.' '.$message."\n");
        }
    }


}
