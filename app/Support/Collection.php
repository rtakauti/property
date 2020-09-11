<?php

declare(strict_types=1);

namespace Rtakauti\Support;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;

class Collection implements ArrayAccess, Countable, JsonSerializable, Iterator
{
    protected $items;
    protected $position;

    public function __construct(array $items = [])
    {
        $this->position = 0;
        $this->items = $items;
    }

    public function offsetExists($offset)
    {
        if (is_integer($offset) || is_string($offset)) {
            return array_key_exists($offset, $this->items);
        }

        return false;
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function count()
    {
        return count($this->items);
    }

    public function jsonSerialize()
    {
        return json_encode($this->items);
    }

    public function current()
    {
        return $this->items[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }
}
