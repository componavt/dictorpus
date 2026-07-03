<?php

namespace App\Traits\Methods;

trait getModelName
{
    public static function getModelName()
    {
        return snake_case(class_basename(get_called_class()));
    }
}
