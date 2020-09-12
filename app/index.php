<?php


namespace Rtakauti;

ini_set('memory_limit', '12M');

require_once __DIR__ . '/../vendor/autoload.php';

use Exception;
use Rtakauti\Support\MicroTimer;
use Rtakauti\Support\CreateJsonFile;
use Rtakauti\Support\Collection;
use Rtakauti\Support\LineIterator;

$microTimer = new MicroTimer();

$microTest = static function () {
    $service = new CreateJsonFile('property.json', 2);
    $service->save();
    $service = null;
};

$microTest1 = static function () {
    $collection = new Collection();
    try {
        $lines = new LineIterator('property.json');
    }catch (Exception $exception){
        die($exception->getMessage());
    }
    foreach ($lines as $line) {
        try {
            $collection[] = $trimmed = trim($line," \n\r,][");
        }catch (Exception $exception){
            die($exception->getMessage());
        }
        echo $trimmed. PHP_EOL;
    }
    echo count($collection) . PHP_EOL;
    $collection = null;
};


$microTimer->timer($microTest);
$microTimer->timer($microTest1);
