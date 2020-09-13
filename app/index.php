<?php
declare(strict_types=1);


namespace Rtakauti;

ini_set('memory_limit', '12M');

require_once __DIR__ . '/../vendor/autoload.php';

use Exception;
use Rtakauti\Support\Connection;
use Rtakauti\Support\CreateJsonFile;
use Rtakauti\Support\LineIterator;
use Rtakauti\Support\MicroTimer;
use Rtakauti\Support\PropertyList;

$microTimer = new MicroTimer();

$microTest = static function () {
    $connection = new Connection(2);
    $connection->setDomain('grupozap-code-challenge.s3-website-us-east-1.amazonaws.com');
    $connection->setEndpoint('/sources/source-2.json');
    $connection->setPort(80);
    $service = new CreateJsonFile('property.json');
    $service->setGenerator($connection->getGenerator());
    $service->create();
    $service = null;
};

$microTest1 = static function () {
    $propertyList = new PropertyList();
    try {
        $lines = new LineIterator('property.json');
        foreach ($lines as $line) {
            $propertyList[] = $line;
        }
    } catch (Exception $exception) {
        die($exception->getMessage());
    }
    foreach ($propertyList as $item) {
        echo $item->jsonSerialize() . PHP_EOL;
        echo $item->getCreatedAt()->format('d/m/Y') . PHP_EOL;
        echo $item->getAddress()->getGeoLocation()->getLocation()->lon . PHP_EOL;
        echo $item->getPricingInfos()->getPrice(). PHP_EOL;
        echo $item->getPricingInfos()->getMonthlyCondoFee(). PHP_EOL;
        echo $item->getImages(0). PHP_EOL;
    }
    echo count($propertyList) . PHP_EOL;
    $propertyList = null;
};


$microTimer->timer($microTest);
$microTimer->timer($microTest1);
