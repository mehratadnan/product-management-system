<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\History;

class Product extends Model
{
    use HasFactory;

    public const CHUNK_SIZE = 20;
    public $timestamps = true;

    public const PRODUCT_STATUS_SALE = "sale";
    public const PRODUCT_STATUS_HIDDEN = "hidden";
    public const PRODUCT_STATUS_OUT = "out";
    public const PRODUCT_STATUS_DELETED = "deleted";

    public const DEFAULT_CURRENCY = "SAR";


    public const STATUS = [
        self::PRODUCT_STATUS_SALE,
        self::PRODUCT_STATUS_HIDDEN,
        self::PRODUCT_STATUS_OUT,
        self::PRODUCT_STATUS_DELETED
    ];

    public $fillable = [
        'productID',
        'quantity',
        'price',
        'merchantID',
        'name',
        'description',
        'status',
        'photo_url',
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
     * @return string
     */
    public function getDescriptione(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): Product
    {
        $this->description = strtolower($description);
        return $this;
    }

    /**
     * @return string
     */
    public function getPhotoUrl(): string
    {
        return $this->photo_url;
    }

    /**
     * @param string $photo_url
     * @return $this
     */
    public function setPhotoUrl(string $photo_url): Product
    {
        $this->photo_url = $photo_url;
        return $this;
    }

    /**
     * @return int
     */
    public function getMerchantID(): int
    {
        return $this->merchantID;
    }

    /**
     * @param string $merchantID
     * @return $this
     */
    public function setMerchantID(string $merchantID): Product
    {
        $this->merchantID = $merchantID;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductID(): int
    {
        return $this->productID;
    }

    /**
     * @param string $productID
     * @return $this
     */
    public function setProductID(string $productID): Product
    {
        $this->productID = $productID;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     * @return $this
     */
    public function setQuantit(string $quantity): Product
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return ucfirst($this->name);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): Product
    {
        $this->name = strtolower($name);
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Product
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return floatval($this->price);
    }

    /**
     * @param $price
     * @return $this
     */
    public function setPrice($price): Product
    {
        $this->price = floatval($price);
        return $this;
    }

    
    /**
     * @param array $data
     * @return array
     */
    public function validateData(array $data): array
    {
        $date = new DateTime('now');
        $formattedDate = $date->format("Y-m-d H:i:s");

        if(empty($data['id']) || empty($data['quantity']) || empty($data['price'])){
            return ['success' => false];
        }

        $product = [
            'productID' => $data['id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'merchantID' => $data['merchantID'],
            'created_at' => $formattedDate,
            'updated_at' => $formattedDate,
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => self::PRODUCT_STATUS_SALE,
            'photo_url' => $data['photo_url'] ?? null
        ];
        return ['success' => true, 'data'=> $product];
    }

    /**
     * @param int $merchantID
     * @return array
     */
    public function getInsertedProductIDs(int $merchantID): array
    {
        try {
            $productIDs = Product::where('merchantID', $merchantID)
                ->where('status', '!=', PRODUCT::PRODUCT_STATUS_DELETED)
                ->pluck('productID', 'productID')
                ->toArray();
            return [
                'success' => true,
                'data' => $productIDs
            ];
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
     * @param array $data
     * @param array $productIDs
     * @return array
     */
    public function compareProductArrays(array $data, array $productIDs): array
    {
        try {
            if(empty($data)){
                return ['success' => false, 'message' => 'Data Is Empty'];
            }
            $productsToDelete = array_diff_key($productIDs,$data);
            $productsToInsert = array_diff_key($data,$productIDs);
            $productsTonNotUpdate = $productsToDelete + $productsToInsert;
            $productsToUpdate = array_diff_key($data,$productsTonNotUpdate);
            return [
                'success' => true,
                'data' => [
                    'productsToDelete' => $productsToDelete,
                    'productsToInsert' => $productsToInsert,
                    'productsToUpdate' => $productsToUpdate,
                ]
            ];
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
     * @param array|null $productsToInsert
     * @return array|bool[]
     */
    public function insertMultipleProducts(?array $productsToInsert): array
    {
        try {
            if(empty($productsToInsert)){
                return ['success' => false, 'message' => 'Data Is Empty'];
            }
            collect($productsToInsert)->chunk(self::CHUNK_SIZE)->each(function ($chunk) {
                Product::insert($chunk->toArray());
            });
            return ['success' => true];
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
     * @param array|null $productsToDelete
     * @param int $merchantID
     * @return array|bool[]
     */
    public function deleteProducts(?array $productsToDelete, int $merchantID): array
    {
        try {
            if(empty($productsToDelete)){
                return ['success' => false, 'message' => 'Data Is Empty'];
            }
            Product::where('merchantID', $merchantID)
                ->whereIn('productID', array_values($productsToDelete))
                ->update([
                    'status' => Product::PRODUCT_STATUS_DELETED,
                ]);
            return ['success' => true];
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
     * @param array $productsToUpdate
     * @param int $merchantID
     * @return array|bool[]
     */
    public function updateProducts(array $productsToUpdate, int $merchantID): array
    {
        try {
            if(empty($productsToUpdate)){
                return ['success' => false, 'message' => 'Data Is Empty'];
            }
            foreach ($productsToUpdate as $product){
                $productID = $product['productID'];
                unset($product['productID']);
                Product::where('merchantID', $merchantID)
                    ->where('productID', $productID)
                    ->update($product);
            }
            return ['success' => true];
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
