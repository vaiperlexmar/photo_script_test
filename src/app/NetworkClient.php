<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\DownloadException;

class NetworkClient
{
    private int $timeout = 10;

    public function get(string $path, array $params = []): array|int
    {
        try {
            $response = @file_get_contents($path);

            return ["file" => $response, "status" => $this->getRequestStatus($http_response_header)];
        } catch (DownloadException $e) {
            return $this->getRequestStatus($http_response_header);
        }
    }

    private function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    private function getRequestStatus($http_response_header): int
    {
        preg_match('/([0-9])\d+/', $http_response_header[0], $matches);
        $responsecode = intval($matches[0]);
        return $responsecode;
    }

}