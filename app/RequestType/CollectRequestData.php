<?php

namespace App\RequestType;

use App\Models\ImportRequest;
use App\Models\Product;
use Exception;
use Illuminate\Support\Str;
use App\Models\History;

/**
 *
 */
class CollectRequestData
{
    /**
     * @param ImportRequest $importRequest
     * @return array
     */
    public function collectData(ImportRequest $importRequest): array
    {
        try {
            
            $className = "App\\RequestType\\RequestType".Str::upper($importRequest->getType());
            if (!class_exists($className)) {
                throw new Exception('Class Not found');
            }
           
            $RequestType = new $className();
            $result = $RequestType->getData($importRequest->getPath(),$importRequest->getPathType());
            if(empty($result['success'])){
                throw new Exception($result['message']);
            }

            $product = new Product();

            $dataSet = $result['data'];
            $corruptData = [];
            $importableData = [];

            foreach ($dataSet as $data){
                $data['merchantID'] = $importRequest->getMerchantID();
                $validatedData = $product->validateData($data);
                if(empty($validatedData['success'])){
                    $corruptData[] = $data;
                }else{
                    $importableData[$validatedData['data']['productID']] = $validatedData['data'];
                }
            }

            return ['success' => true, 'data' => [
                'corrupt_data' => $corruptData,
                'importable_data' => $importableData,
            ]];

        }catch (\Exception $e){
            (new History())
                ->setClass(__CLASS__)
                ->setMethod(__METHOD__)
                ->setRef($e->getLine())
                ->setMessage($e->getMessage())
                ->save();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

}

