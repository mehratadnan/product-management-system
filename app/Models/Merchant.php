<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\History;

class Merchant extends Model
{
    use HasFactory;

    public $timestamps = true;

    public $fillable = [
        'name',
        'email',
        'password',
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): Merchant
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): Merchant
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): Merchant
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return array
     */
    public function getMerchantList(): array
    {
        try {
            $merchantList = Merchant::all();
            return ['success' => true, 'data' => $merchantList];
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
