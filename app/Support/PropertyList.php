<?php
declare(strict_types=1);


namespace Rtakauti\Support;


use Rtakauti\DTO\PropertyDTO;

class PropertyList extends Collection
{
    public function offsetGet($offset): PropertyDTO
    {
        return new PropertyDTO(parent::offsetGet($offset));
    }

    public function current(): PropertyDTO
    {
        return new PropertyDTO(parent::current());
    }
}