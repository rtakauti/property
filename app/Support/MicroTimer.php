<?php
declare(strict_types=1);


namespace Rtakauti\Support;


class MicroTimer
{

    final public function timer(callable $microTest): void
    {
        $initialTime = microtime(true);
        $microTest();
        echo number_format((microtime(true) - $initialTime) * 1000, 2, ',', '.'), "ms\r\n";
    }

}