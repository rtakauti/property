<?php
declare(strict_types=1);


namespace Rtakauti\Support;


use Exception;
use Generator;
use Rtakauti\DTO\PropertyDTO;

class PropertyList extends Collection
{
    use PaginateableTrait;

    public function __construct(array $items = [])
    {
        parent::__construct($items);
        foreach (new LineIterator('property.json') as $line) {
            $this[] = $line;
        }
    }

    public function offsetSet($offset, $value): void
    {
        $charList = " \n\r,][";
        if (is_null($offset)) {
            $this->items[] = trim($value, $charList);
        } else {
            $this->items[$offset] = trim($value, $charList);
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

    public function jsonSerialize(): Generator
    {
        foreach ($this->items as $item) {
            yield $item;
        }
    }
}