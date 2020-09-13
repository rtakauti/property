<?php
declare(strict_types=1);


namespace Rtakauti\DTO;


class PricingInfoDTO
{

    private array $attribute;

    public function __construct(array $attribute)
    {
        $this->attribute = $attribute;
    }

    public function getBusinessType()
    {
        return $this->attribute['businessType'];
    }

    public function __call($name, $arguments)
    {
        $key = (strpos($name, 'get') !== false) ? lcfirst(str_replace('get', '', $name)) : '';
        return array_key_exists($key, $this->attribute) ? 'R$'.number_format( (float)$this->attribute[$key],2,',','.') : '';
    }
}