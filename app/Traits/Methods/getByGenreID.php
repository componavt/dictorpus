<?php namespace App\Traits\Methods;

trait getByGenreID
{
    public static function getByGenreID($genre_id)
    {
        return self::whereIn('genre_id',(array)$genre_id)
                   ->orderBy('sequence_number')->get();
    }
}