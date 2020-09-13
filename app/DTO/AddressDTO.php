<?php
declare(strict_types=1);


namespace Rtakauti\DTO;


class AddressDTO
{

    private array $attribute;

    public function __construct(array $attribute)
    {
        $this->attribute = $attribute;
    }

    public function getGeoLocation(): GeoLocationDTO
    {
        return new GeoLocationDTO($this->attribute['geoLocation']);
    }

    public function __call($name, $arguments)
    {
        $key = (strpos($name, 'get') !== false) ? lcfirst(str_replace('get', '', $name)) : '';
        return array_key_exists($key, $this->attribute) ? $this->attribute[$key] : '';
    }


}