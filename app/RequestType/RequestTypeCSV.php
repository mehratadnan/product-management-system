<?php

namespace App\RequestType;


use App\Http\Helpers\FileProvider;
use Exception;
use App\Models\History;

/**
 *
 */
class RequestTypeCSV extends RequestTypeAbstract
{
    /**
     * @param string $source
     * @return array
     */
    public function getData(string $source, string $pathType): array
    {
        try {
            $result = (new FileProvider)->openFile($source, $pathType);
            if(empty($result['success'])){
                throw new Exception($result['message']);
            }

            $source = $result['data'];
            $dataSet = [];
            while (($data = fgetcsv($source)) !== FALSE) {
                $dataSet[] = $data;
            }

            (new FileProvider)->closeFile($source);

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

