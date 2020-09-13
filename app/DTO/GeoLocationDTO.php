<?php
declare(strict_types=1);


namespace Rtakauti\DTO;


class GeoLocationDTO
{

    private array $attribute;

    public function __construct(array $attribute)
    {
        $this->attribute = $attribute;
    }

    public function getLocation(): object
    {
        return (object)$this->attribute['location'];
    }

    public function __call($name, $arguments)
    {
        $key = (strpos($name, 'get') !== false) ? lcfirst(str_replace('get', '', $name)) : '';
        return array_key_exists($key, $this->attribute) ? $this->attribute[$key] : '';
    }
}