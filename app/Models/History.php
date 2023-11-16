<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $table = 'history';
    public $timestamps = true;

    public $fillable = [
        'id',
        'class',
        'method',
        'ref',
        'message',
        'extra',
        'updated_at',
        'created_at'
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
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function setClass(string $class): History
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): History
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $ref
     * @return $this
     */
    public function setRef(string $ref): History
    {
        $this->ref = $ref;
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
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): History
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtra(): string
    {
        return $this->extra;
    }

    /**
     * @param string $extra
     * @return $this
     */
    public function setExtra(string $extra): History
    {
        $this->extra = $extra;
        return $this;
    }

}
