<?php

namespace App\Providers;
use App\Models\ImportRequest;

class FileProvider
{
    /**
     * @param string $path
     * @param string $pathType
     * @return array
     */
    public function openFile(string $path, string $pathType): array
    {
        try {
            if($pathType == ImportRequest::PATH_REMOTE){
                return ['success' => true, 'data' => file_get_contents($path)];
            }

            if (!file_exists($path)) {
                return ['success' => false, 'message' => 'File Not Found'];
            }
            return ['success' => true, 'data' => file_get_contents($path)];
        }catch (\Exception $e){
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param $file
     * @return array|bool[]
     */
    public function closeFile($file): array
    {
        try {
            fclose($file);
            return ['success' => true];
        }catch (\Exception $e){
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
