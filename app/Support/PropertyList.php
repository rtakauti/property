<?php
declare(strict_types=1);


namespace Rtakauti\Support;


use Rtakauti\DTO\PropertyDTO;

class PropertyList extends Collection
{
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
        return new PropertyDTO(parent::offsetGet($offset));
    }

    public function current(): PropertyDTO
    {
        return new PropertyDTO(parent::current());
    }
}