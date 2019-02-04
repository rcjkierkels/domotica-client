<?php

namespace App\Collections;

use Illuminate\Support\Collection;

abstract class TypeCollection extends Collection
{

    public function __construct($items = [])
    {
        $this->validateItems($items);
        parent::__construct($items);
    }

    protected function validateItems($items = [])
    {
        $allowedType = $this->getValidType();

        foreach($items as $item)
        {
            if (!$item instanceof $allowedType) {
                throw new \Exception('Trying to add items of invalid type to typed Collection');
            }
        }
    }

    public function offsetSet($key, $value)
    {
        $this->validateItems([$value]);
        return parent::offsetSet($key, $value);
    }

    abstract function getValidType() : string;
}
