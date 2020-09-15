<?php
declare(strict_types=1);


namespace Rtakauti\DTO;

use DateTime;
use DateTimeZone;
use Exception;
use JsonSerializable;

abstract class AbstractDTO implements JsonSerializable
{

    protected array $attribute;

    public function __construct(array $attribute)
    {
        $this->attribute = $attribute;
    }

    public function jsonSerialize()
    {
        try {
            return json_encode($this->attribute, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|string
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        $attribute = $this->cleanGet($name);
        $class = __NAMESPACE__ . '\\' . ucfirst($attribute) . 'DTO';
        $result = '';
        if (array_key_exists($attribute, $this->attribute)) {
            $result = $this->attribute[$attribute];
        }
        if (empty($result)) {
            return '';
        }
        if (is_array($result)) {
            return new $class($result);
        }
        if (is_numeric($result)) {
            return $result;
        }
        try {
            if (!is_bool($date = new DateTime($result, new DateTimeZone('America/Sao_Paulo')))) {
                return $date;
            }
        } catch (Exception $e) {
        }
        return $result;
    }

    /**
     * @param string $method
     * @param string $prefix
     * @return string
     */
    protected function cleanGet(string $method, string $prefix = 'get'): string
    {
        return (strpos($method, $prefix) !== false) ? lcfirst(str_replace($prefix, '', $method)) : '';
    }
}