<?php

namespace App\RequestType;


interface RequestTypeInterface
{
    /**
     * @param string $source
     * @param string $pathType
     * @return array
     */
    public function getData(string $source, string $pathType):array;
}
