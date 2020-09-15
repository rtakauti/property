<?php
declare(strict_types=1);


namespace Rtakauti\DTO;


class PricingInfosDTO extends AbstractDTO
{
    public function __call($name, $arguments)
    {
        return is_numeric($result = parent::__call($name, $arguments)) ? 'R$ ' . number_format((float)$result, 2, ',', '.') : $result;
    }
}