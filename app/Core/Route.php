<?php
declare(strict_types=1);


namespace Rtakauti\Core;


class Route
{
    private string $verb;
    private string $route;

    public function __construct()
    {
        $this->verb = $_SERVER['REQUEST_METHOD'];
        $this->route = trim($_SERVER['REQUEST_URI'], '/');
    }

    public function __call($name, $arguments): void
    {
        if ($this->verb !== strtoupper($name) || strpos($this->route, $arguments[0]) !== 0) {
            return;
        }
        $param = trim(str_replace($arguments[0], '', $this->route), '/');
        (new $arguments[1])->{$arguments[2]}($param);
    }
}