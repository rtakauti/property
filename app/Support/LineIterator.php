<?php
declare(strict_types=1);


namespace Rtakauti\Support;


use Iterator;

class LineIterator implements Iterator
{
    protected $handler;
    protected string $line;
    protected int $position;
    private bool $validation;

    public function __construct($fileName)
    {
        if (!$this->handler = fopen(__DIR__ . '/../assets/' . $fileName, 'rb')) {
            die('Could not open file "' . $fileName . '"' . PHP_EOL);
        }
        $this->validation = true;
    }

    public function rewind(): void
    {
        fseek($this->handler, 0);
        $this->position = 0;
        $this->line = fgets($this->handler);
    }

    public function valid(): bool
    {
        return $this->validation;
    }

    public function current(): string
    {
        return $this->line;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->validation = false;
        if (is_string($line = fgets($this->handler))) {
            $this->line = $line;
            $this->position++;
            $this->validation = true;
        }
    }

    public function __destruct()
    {
        fclose($this->handler);
    }
}
