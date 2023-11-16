<?php

namespace App\Http\Controllers;

use App\Models\ImportRequest;
use Illuminate\Http\Request;

class ImportRequestController extends Controller
{
    public $importRequest;
    /**
     * @var array
     */
    private $params;

    public function __construct(Request $request)
    {
        $this->params = $request->all();

    }

    /**
     * @return array
     */
    public function createImportRequest(): array
    {
        $filePath = $this->params['file_path'] ?? null;
        $merchantID = !empty($this->params['merchant_id']) ? ((int)$this->params['merchant_id']) : null;

        if(empty($filePath) || empty($merchantID)){
            return ['success' => false, 'message' => 'Missing Required Fields'];
        }

        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            $pathType = ImportRequest::PATH_REMOTE;
        }else{
            $pathType = ImportRequest::PATH_LOCAL;
        }

        $explodedFilePath = explode('.',$filePath);
        $type = end($explodedFilePath);

        return (new ImportRequest())->createImportRequest(
            $type,
            $filePath,
            $merchantID,
            $pathType
        );
    }

    /**
     * @return array
     */
    public function deleteImportRequest(): array
    {
        $ref = !empty($this->params['ref']) ? ((string)$this->params['ref']) : null;

        if(empty($ref)){
            return ['success' => false, 'message' => 'Missing Required Fields'];
        }

        return (new ImportRequest())->deleteImportRequest($ref);
    }

    /**
     * @return array
     */
    public function listImportRequest(): array
    {
        $merchantID = (!empty($this->params['merchant_id'])) ? ((int)$this->params['merchant_id']) : null;
        $page = (!empty($this->params['page'])) ? ((int)$this->params['page']) : null;
        $perPage = (!empty($this->params['perPage'])) ? ((int)$this->params['perPage']) : null;

        if(empty($merchantID)){
            return ['success' => false, 'message' => 'Missing Required Fields'];
        }

        return (new ImportRequest())->listImportRequest($merchantID, $page, $perPage);
    }

    /**
     * @return array
     */
    public function selectImportRequest(): array
    {
        $importRequestRef = (!empty($this->params['ref'])) ? ($this->params['ref']) : null;
        if(empty($importRequestRef)){
            return ['success' => false, 'message' => 'Missing Required Fields'];
        }

        return (new ImportRequest())->selectImportRequest($importRequestRef);
    }

    /**
     * @return array
     */
    public function updateImportRequest(): array
    {
        $ref = (!empty($this->params['ref'])) ? ($this->params['ref']) : null;
        $status = (!empty($this->params['status'])) ? ($this->params['status']) : null;
        $message = (!empty($this->params['message'])) ? ($this->params['message']) : null;
        $filePath = $this->params['file_path'] ?? null;
        
        if(empty($ref) || (empty($status) && empty($message) && empty($filePath))){
            return ['success' => false, 'message' => 'Missing Required Fields'];
        }

        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            $type = "url";
            $pathType = ImportRequest::PATH_REMOTE;
        }else{
            $explodedFilePath = explode('.',$filePath);
            $type = end($explodedFilePath);
            $pathType = ImportRequest::PATH_LOCAL;
        }

        return (new ImportRequest())->updateImportRequest(
            $ref,
            $status,
            $message,
            $type,
            $pathType
        );
    }
}
