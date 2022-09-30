<?php namespace App\Traits\Methods\search;

use App\Models\Corpus\Genre;
trait byGenre
{
    public static function searchByGenre($objs, $genres) {
        if (!sizeof($genres)) {
            return $objs;
        }

        foreach (Genre::whereIn('parent_id', $genres)->get() as $g) {
            $genres[] = $g->id;
        }
        
        return $objs->whereIn('genre_id',$genres);
    }
}