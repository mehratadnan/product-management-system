<?php

namespace App\RequestType;

use Exception;

/**
 *
 */
abstract class RequestTypeAbstract implements RequestTypeInterface
{
    const REPORT_FILE_CSV = 'csv';
    const REPORT_FILE_XML = 'xml';
    const REPORT_FILE_XLSX = 'xlsx';

    public static $report_file_types = [
        self::REPORT_FILE_CSV,
        self::REPORT_FILE_XML,
        self::REPORT_FILE_XLSX,
    ];

    public function __construct()
    {

    }

    /**
     * @param string $pathType
     * @return array
     */
    abstract function getData(string $source, string $pathType): array;

}

