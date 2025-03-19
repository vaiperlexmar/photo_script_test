<?php

namespace App\Exceptions;

class FileSaveException extends \Exception
{
    protected $message = 'File save failed';
}