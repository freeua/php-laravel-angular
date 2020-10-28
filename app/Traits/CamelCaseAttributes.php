<?php

namespace App\Traits;

trait CamelCaseAttributes
{
    public function getAttribute($key)
    {
        if ($this->existsAttribute($key) || $this->existsAttribute(snake_case($key))) {
            return parent::getAttribute(snake_case($key));
        }
        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute(snake_case($key), $value);
    }

    private function existsAttribute($key)
    {
        return array_key_exists($key, $this->attributes) ||
            $this->hasGetMutator($key);
    }

    public function toArrayCamel()
    {
        $array = $this->getAttributes();

        foreach ($array as $key => $value) {
            $return[camel_case($key)] = $value;
        }

        return $return;
    }
}
