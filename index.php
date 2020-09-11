<?php


namespace Rtakauti\Support;

ini_set('memory_limit', '20M');

require_once __DIR__ . "/vendor/autoload.php";

use Rtakauti\Support\MicroTimer;
use Rtakauti\Support\SaveJsonFile;
use Rtakauti\Support\Collection;
use Rtakauti\Support\LineIterator;

$microTimer = new MicroTimer();

$microTest = function () {
    $service = new SaveJsonFile('property.json', 2);
    $service->save();
    $service = null;
};

$microTest1 = function () {
    $collection = new Collection();
    $lines = new LineIterator('property.json');
    foreach ($lines as $line)
        $collection[] = $line;
    echo count($collection) . PHP_EOL;
    $collection = null;
};


$microTimer->timer($microTest);
$microTimer->timer($microTest1);
