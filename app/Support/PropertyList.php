<?php
declare(strict_types=1);


namespace Rtakauti\Support;


use Exception;
use Rtakauti\DTO\PropertyDTO;

class PropertyList extends Collection
{
    use PaginateableTrait;

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = trim($value, " \n\r,][");
        } else {
            $this->items[$offset] = trim($value, " \n\r,][");
        }
    }

    public function offsetGet($offset): PropertyDTO
    {
        try {
            return new PropertyDTO(json_decode(parent::offsetGet($offset), true, 512, JSON_THROW_ON_ERROR));
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }

    public function current(): PropertyDTO
    {
        try {
            return new PropertyDTO(json_decode(parent::current(), true, 512, JSON_THROW_ON_ERROR));
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }
}