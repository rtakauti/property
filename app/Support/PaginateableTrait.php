<?php
declare(strict_types=1);


namespace Rtakauti\Support;


use LimitIterator;

trait PaginateableTrait
{

    public function paginate(int $offset, int $qty): LimitIterator
    {
        return new LimitIterator($this, $offset, $qty);
    }
}