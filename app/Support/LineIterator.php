<?php
declare(strict_types=1);


namespace Rtakauti\Support;


use Iterator;
use RuntimeException;

class LineIterator implements Iterator
{
    protected $handler;
    protected $line;
    protected $position;

    public function __construct($fileName)
    {
        if (!$this->handler = fopen($fileName, 'r'))
            throw new RuntimeException('Couldn\'t open file "' . $fileName . '"');
    }

    public function rewind()
    {
        fseek($this->handler, 0);
        $this->line = fgets($this->handler);
        $this->position = 0;
    }

    public function valid()
    {
        return false !== $this->line;
    }

    public function current()
    {
        return $this->line;
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        if (false !== $this->line) {
            $this->line = fgets($this->handler);
            $this->position++;
        }
    }

    public function __destruct()
    {
        fclose($this->handler);
    }
}
