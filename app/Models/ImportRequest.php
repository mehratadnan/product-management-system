<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\History;

class ImportRequest extends Model
{
    use HasFactory;

    const STATUS_NEW = "new";
    const STATUS_PROCESSING = "processing";
    const STATUS_SUCCESS = "success";
    const STATUS_ERROR = "error";

    const PATH_REMOTE = "remote";
    const PATH_LOCAL = "local";

    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_PROCESSING,
        self::STATUS_SUCCESS,
        self::STATUS_ERROR,
    ];

    const PATH_TYPES = [
        self::PATH_REMOTE,
        self::PATH_LOCAL,
    ];

    const PER_PAGE = 10;

    public $timestamps = true;

    public $fillable = [
        'merchantID',
        'type',
        'path',
        'pathType',
        'status',
        'ref',
        'message',
        'created_at',
        'updated_at'
    ];

    /**
     * @return int
     */
    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getMerchantID(): int
    {
        return $this->merchantID;
    }

    /**
     * @param int $merchantID
     * @return $this
     */
    public function setMerchantID(int $merchantID): ImportRequest
    {
        $this->merchantID = $merchantID;
        return $this;
    }
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): ImportRequest
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getRef(): string
    {
        return $this->ref;
    }

    /**
     * @param string $ref
     * @return $this
     */
    public function setRef(string $ref): ImportRequest
    {
        $this->ref = $ref;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $pathType
     * @return $this
     */
    public function setPathType(string $pathType): ImportRequest
    {
        $this->pathType = $pathType;
        return $this;
    }

    /**
     * @return string
     */
    public function getPathType(): string
    {
        return $this->pathType;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): ImportRequest
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): ImportRequest
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $ref
     * @return array
     */
    public function selectImportRequest(string $ref): array
    {
        try {
            $importRequest = ImportRequest::where('ref', $ref)->first()->toArray();
            if(empty($importRequest)){
                return ['success' => true, 'message' => 'No Record Found'];
            }
            return ['success' => true, 'data' => $importRequest];
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

    /**
     * @param int $merchantID
     * @param int|null $page
     * @param int|null $perPage
     * @return array
     */
    public function listImportRequest(int $merchantID, ?int $page, ?int $perPage): array
    {
        try {
            $page = (!empty($page)) ? $page : 1;
            $perPage = (!empty($perPage)) ? $perPage : self::PER_PAGE;
            $importRequests = ImportRequest::where('merchantID', $merchantID)
                ->paginate($perPage, ['*'], 'page', $page);
            return ['success' => true, 'data' => $importRequests];
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

    /**
     * @return array
     */
    public function listNewImportRequest(): array
    {
        try {
            $importRequests = ImportRequest::where('status', self::STATUS_NEW)->get();
            return ['success' => true, 'data' => $importRequests];
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

    /**
     * @param int $importRequestID
     * @return array
     */
    public function deleteImportRequest(string $ref): array
    {
        try {
            $importRequest = ImportRequest::where('ref', $ref)->first();

            if(empty($importRequest)){
                return ['success' => false, 'message' => 'No Record Found'];
            }
            $importRequest->delete();

            return ['success' => true, 'message' => 'Record Has Been Deleted'];
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

    /**
     * @param string $ref
     * @param string|null $status
     * @param string|null $message
     * @return array|bool[]
     */
    public function updateImportRequest(string $ref, ?string $status, ?string $message = "", ?string $type = "", ?string $pathType = ""): array
    {
        try {
            $importRequest = ImportRequest::where("ref", $ref)->first();
            if(empty($importRequest)){
                return ['success' => false, 'message' => "Import Request Record Was Not Found "];
            }

            if(!empty($message)){
                $importRequest->message = $message;
            }

            if(!empty($status) && in_array($status,self::STATUSES)){
                $importRequest->status = $status;
            }

            if(!empty($pathType)){
                $importRequest->pathType = $pathType;
            }

            if(!empty($type)){
                $importRequest->type = $type;
            }

            if(!empty($message) || !empty($status)){
                $importRequest->save();
            }

            return ['success' => true, 'message' => 'Record Has Been Updated'];
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

    /**
     * @param string $type
     * @param string $path
     * @param string $pathType
     * @param int $merchantID
     * @return array
     */
    public function createImportRequest(string $type, string $path, int $merchantID, string $pathType): array
    {
        try {
            $ref = $this->createRef();
            $importRequest = new ImportRequest;
            $importRequest->setType($type)
                ->setPath($path)
                ->setRef($ref)
                ->setMerchantID($merchantID)
                ->setStatus(self::STATUS_NEW)
                ->setPathType($pathType)
                ->save();
            return ['success' => true, 'data' => ['ref' => $ref]];
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

    /**
     * @return string
     */
    public static function createRef(): string
    {
        $prefix = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 4);
        $suffix = substr(str_shuffle('0123456789'), 0, 4);
        $uniqid = uniqid();
        return "{$prefix}_{$suffix}_{$uniqid}";
    }


}
