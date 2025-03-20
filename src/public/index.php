<?php

declare(strict_types=1);

use App\FileDownloader;
use App\FileHandler;
use App\Logger;
use App\NetworkClient;

require_once __DIR__ . "/../../vendor/autoload.php";

$fileHandler = new FileHandler();
$networkClient = new NetworkClient();

$fileDownloader = new FileDownloader($fileHandler, $networkClient, __DIR__ . "/../images.txt");
$fileDownloader->download();
