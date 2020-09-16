<?php
declare(strict_types=1);


namespace Rtakauti\Core;

use Rtakauti\Controller\ApiController;

$route = new Route();

$route->get('properties', ApiController::class, 'getAll');


header("HTTP/1.1 404 Not found");
echo 'Address not found';
die();