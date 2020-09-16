<?php
declare(strict_types=1);


namespace Rtakauti;

ini_set('memory_limit', '12M');
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Rtakauti\Support\Connection;
use Rtakauti\Support\CreateJsonFile;
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
    unset($service);
};

$microTest1 = static function () {
    foreach (($propertyList = new PropertyList())->paginate(0, 10) as $key => $property) {
        echo $key . PHP_EOL;
//        echo $property->getId() . PHP_EOL;
        echo $property->getUsableAreas() . PHP_EOL;
//        echo $property->jsonSerialize() . PHP_EOL;
//        echo $property->getCreatedAt()->format('d/m/Y H:i:s') . PHP_EOL;
//        echo $property->getAddress()->getGeoLocation()->getLocation()->getLon() . PHP_EOL;
//        echo $property->getAddress()->getGeoLocation()->jsonSerialize() . PHP_EOL;
//        echo $property->getAddress()->getGeoLocation()->getLocation()->jsonSerialize() . PHP_EOL;
//        echo $property->getAddress()->jsonSerialize().PHP_EOL;
//        echo $property->getPricingInfos()->getPrice() . PHP_EOL;
//        echo $property->getPricingInfos()->getMonthlyCondoFee() . PHP_EOL;
//        echo $property->getPricingInfos()->jsonSerialize().PHP_EOL;
//        print_r($property->getImages()). PHP_EOL;
//        echo $property->getImage(0) . PHP_EOL;
    }
    echo count($propertyList) . PHP_EOL;
    echo '[';
    foreach ($propertyList->jsonSerialize() as $key => $property) {
        echo $key < count($propertyList) - 1 ? $property . ',' : $property;
    }
    echo ']' . PHP_EOL;
    unset($propertyList);
};


$microTimer->timer($microTest);
$microTimer->timer($microTest1);
