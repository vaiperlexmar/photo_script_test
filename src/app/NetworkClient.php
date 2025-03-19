<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\DownloadException;

class NetworkClient
{
    private int $timeout = 10;

    public function get(string $path, array $params = []): bool|string
    {
        $logger = Logger::getLogger();

        set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($logger) {
            $logger->log($errno, $errstr, $errfile, $errline);
        });

        try {
            $response = file_get_contents($path);

            return $response;
        } catch (DownloadException $e) {
            var_dump($e->getMessage());
        }
    }

    private function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

}