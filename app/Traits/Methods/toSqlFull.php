<?php namespace App\Traits\Methods;

trait toSqlFull
{
    function toSqlFull() : String
    {
            $query = str_replace(array('?'), array('\'%s\''), $this->toSql());
            $query = vsprintf($query, $this->getBindings());     
            return $query;
    }
}