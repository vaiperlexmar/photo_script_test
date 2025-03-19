<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\DownloadException;

class FileDownloader
{
    public string $path = "./download";
    public array $headers = [];
    private FileHandler $fileHandler;
    private NetworkClient $networkClient;
    private Logger $logger;
    private string $linksFilePath;
    private array $linksUrl;

    public function __construct(
        FileHandler $fileHandler,
        NetworkClient $networkClient,
        Logger $logger,
        string $linksFilePath
    ) {
        $this->fileHandler = $fileHandler;
        $this->networkClient = $networkClient;
        $this->logger = $logger;
        $this->linksFilePath = $linksFilePath;
    }

    public function download(): void
    {
        $this->getLinks($this->linksFilePath);

        if ($this->linksUrl) {
            if (!is_dir($this->path)) {
                $this->fileHandler->createDirectory($this->path);
            }
            foreach ($this->linksUrl as $url) {
                try {
                    $url = trim($url);
                    
                    $response = $this->networkClient->get($url);
                    if (!$response) {
                        $this->logger->log($response);
                        throw new DownloadException;
                    }

                    $this->fileHandler->save($response, $this->path, $this->getFilename($url));
                } catch (\Exception $exception) {
                    var_dump($exception->getMessage());
                }
            }
        } else {
            $this->logger->log("No links found");
        }
    }

    private function getLinks(string $linksFilePath): void
    {
        if (file_exists($linksFilePath)) {
            $urls = explode("\n", file_get_contents($linksFilePath));
            $this->linksUrl = $urls;
        } else {
            $this->logger->log("No file found on $linksFilePath");
        }
    }

    public function setDestination(string $path): void
    {
        $this->path = $path;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    private function getFilename(string $url): string
    {
        $substrings = explode("/", $url);

        return end($substrings);
    }

    private function setLinksFilePath(string $linksFilePath): void
    {
        $this->linksFilePath = $linksFilePath;
    }

}