<?php
declare(strict_types=1);


namespace Rtakauti\DTO;


class PropertyDTO extends AbstractDTO
{

    public function getImage(int $key): string
    {
        return $this->getImages()[$key] ?? '';
    }

    public function getImages(): array
    {
        return $this->attribute[$this->cleanGet(__METHOD__, __CLASS__ . '::get')] ?? [];
    }
}