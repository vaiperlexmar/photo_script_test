<?php

namespace App\Exceptions;

class DownloadException extends \Exception
{
    protected $message = 'Download failed';

    public static string $fileNotFound = "URL is not valid, file not found";
}