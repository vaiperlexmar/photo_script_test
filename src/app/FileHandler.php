<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\FileSaveException;

class FileHandler
{
    public function save(string $data, string $path, string $filename): string|bool|int
    {
        try {
            if (file_exists($path."/".$filename)) {
                return "duplicate";
            }
            $result = file_put_contents($path."/".$filename, $data, FILE_USE_INCLUDE_PATH);

            if (!$result) {
                throw new FileSaveException;
            }

            return $result;
        } catch (FileSaveException $exception) {
            echo 'Caught exception: ', $exception->getMessage(), "\n";;
        }
    }

    public function createDirectory(string $path): void
    {
        mkdir($path, 0777, true);
    }
}