<?php
declare(strict_types=1);


namespace Rtakauti\DTO;


use DateTime;
use DateTimeZone;
use Exception;
use JsonSerializable;

class PropertyDTO implements JsonSerializable
{
    private array $attribute;

    public function __construct(string $attribute)
    {
        try {
            $this->attribute = json_decode($attribute, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }


    public function __call($name, $arguments)
    {
        $key = (strpos($name, 'get') !== false) ? lcfirst(str_replace('get', '', $name)) : '';
        $item = array_key_exists($key, $this->attribute) ? $this->attribute[$key] : die('There is no method "' . $name . '"');
        if ($key === 'createdAt' || $key === 'updatedAt') {
            try {
                return new DateTime($item, new DateTimeZone('America/Sao_Paulo'));
            } catch (Exception $exception) {
                die($exception->getMessage());
            }
        }
        return $item;
    }

    public function jsonSerialize()
    {
        try {
            return json_encode($this->attribute, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }
}