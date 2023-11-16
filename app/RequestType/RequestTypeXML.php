<?php

namespace App\RequestType;


use App\Providers\FileProvider;
use App\Models\ImportRequest;
use Exception;
use App\Models\History;

/**
 *
 */
class RequestTypeXML extends RequestTypeAbstract
{
    /**
     * @param string $pathType
     * @param string $source
     * @return array
     */
    public function getData(string $source, string $pathType): array
    {
        try {
            $fileProvider = new FileProvider();
            $result = $fileProvider->openFile($source, $pathType);
            if(empty($result['success'])){
                throw new Exception($result['message']);
            }

            $xml = simplexml_load_string($result['data']);
            $dataSet = [];
            foreach ($xml->product as $product) {
                if(!empty($product)){
                    $data = [
                        'id' => (int)($product->id ?? 0),
                        'name' => (string)($product->name ?? ""),
                        'description' => (string)($product->description ?? ""),
                        'price' => (float)($product->price ?? 0.0),
                        'quantity' => (int)($product->quantity ?? 0),
                        'photo_url' => (string)($product->photo_url ?? ""),
                    ];
                    $dataSet[(int)$product->id] = $data;
                }
            }
            return ['success' => true, 'data' => $dataSet];
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

