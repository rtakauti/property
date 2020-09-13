<?php

declare(strict_types=1);

namespace Rtakauti\Support;

use ArrayAccess;
use Countable;
use Iterator;

abstract class Collection implements ArrayAccess, Countable, Iterator
{
    protected array $items;
    protected int $position;

    public function __construct(array $items = [])
    {
        $this->position = 0;
        $this->items = $items;
    }

    public function offsetExists($offset): bool
    {
        if (is_int($offset) || is_string($offset)) {
            return array_key_exists($offset, $this->items);
        }

        return false;
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function current()
    {
        return $this->items[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key():int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
