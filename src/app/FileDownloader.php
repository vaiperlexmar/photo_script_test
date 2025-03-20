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
    private string $linksFilePath;
    private array $linksUrl;
    private int $succeedDownloadsCounter = 0;
    private int $failedDownloadsCounter = 0;
    private int $skippedDownloadsCounter = 0;

    public function __construct(
        FileHandler   $fileHandler,
        NetworkClient $networkClient,

        string        $linksFilePath
    )
    {
        $this->fileHandler = $fileHandler;
        $this->networkClient = $networkClient;

        $this->linksFilePath = $linksFilePath;
    }

    public function download(): void
    {
        Logger::createLogFile();
        Logger::setLogLevel(2);
        Logger::setStartWorkingScript();

        $this->getLinks($this->linksFilePath);

        if ($this->linksUrl) {
            if (!is_dir($this->path)) {
                $this->fileHandler->createDirectory($this->path);
            }
            foreach ($this->linksUrl as $url) {
                try {
                    $url = trim($url);

                    $response = $this->networkClient->get($url);
                    if (!$response["file"]) {
                        $this->failedDownloadsCounter++;
                        Logger::logLinkResult($url, LinkStatus::FAILURE, $response["status"]);
                        throw new DownloadException;
                    }

                    $downloadedFile = $this->fileHandler->save($response["file"], $this->path, $this->getFilename($url));
                    if ($downloadedFile === "duplicate") {
                        $this->skippedDownloadsCounter++;
                        Logger::logLinkResult($url, LinkStatus::SKIPPED, 200);
                    } else {
                        $this->succeedDownloadsCounter++;
                        Logger::logLinkResult($url, LinkStatus::SUCCESS, 200, filesize($this->path . "/" . $this->getFilename($url)));
                    }
                } catch (\Exception $exception) {
                    var_dump($exception->getMessage());
                }
            }
        } else {
            Logger::warning("No links found");
        }

        Logger::info("Downloading is finished", ["succeed" => $this->succeedDownloadsCounter, "failed" => $this->failedDownloadsCounter, "skipped" => $this->skippedDownloadsCounter]);
        Logger::closeFile();

        $this->resetCounters();
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

    private function resetCounters()
    {
        $this->succeedDownloadsCounter = 0;
        $this->failedDownloadsCounter = 0;
        $this->skippedDownloadsCounter = 0;
    }
}