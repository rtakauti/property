<?php
declare(strict_types=1);


namespace Rtakauti\Controller;


use Rtakauti\Support\Connection;
use Rtakauti\Support\CreateJsonFile;

trait EtagSupport
{
    private function etagManage(): void
    {
        $connection = new Connection(2);
        $connection->setDomain('grupozap-code-challenge.s3-website-us-east-1.amazonaws.com');
        $connection->setEndpoint('/sources/source-2.json');
        $connection->setPort(80);
        $service = new CreateJsonFile('property.json');
        $service->setGenerator($connection->getGenerator());
        $service->create();
        unset($connection, $service);
        $lastModified = filemtime(__DIR__ . '/../assets/property.json');
        $etagFile = md5_file(__DIR__ . '/../assets/property.json');
        $etagHeader = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        header("Last-Modified: " . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
        header("Etag: $etagFile");
        header('Cache-Control: public');
        header('Content-Type: application/json; charset=utf-8');
        header('Connection: Keep-Alive');
        if ($etagHeader === $etagFile) {
            header("HTTP/1.1 304 Not Modified");
            die();
        }
    }
}