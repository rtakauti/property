<?php
declare(strict_types=1);


namespace Rtakauti\Controller;


use Rtakauti\Support\PropertyList;

class ApiController
{
    use EtagSupport;

    public function getAll(): void
    {
        $this->etagManage();

        echo '[';
        foreach (($propertyList = new PropertyList())->jsonSerialize() as $key => $property) {
            echo $key < count($propertyList) - 1 ? $property . ',' : $property;
        }
        echo ']';
        die();
    }

    public function getOne(string $param): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo $param;
        die();
    }
}