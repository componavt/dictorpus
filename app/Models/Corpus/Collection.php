<?php
namespace App\Models\Corpus;

use App\Models\Corpus\Genre;

class Collection {
    public static function getCollectionGenres($collection_id=null) {
        $genres = [1=>19, 2=>66, 3=>60];
        if (!$collection_id) {
            return $genres;
        }
        if (isset($genres[$collection_id])) {
            return $genres[$collection_id];
        }
    }

    public static function getCollectionLangs($collection_id=null) : array {
        $langs = [1=>[1], 2=>[4,5,6]];
        if (!$collection_id) {
            return $langs;
        }
        if (isset($langs[$collection_id])) {
            return $langs[$collection_id];
        }
        return [];
    }

    public static function getCollectionIds() {
        return array_keys(self::getCollectionGenres());
    }

    public static function isCollectionId($id) : bool {
        $genres = self::getCollectionGenres();
        if (isset($genres[$id])) {
            return true;
        }
    }
    
    public static function getCollectionId($lang_ids, $genre_ids) {
        $lang_ids = (array)$lang_ids;
        
        foreach (self::getCollectionGenres() as $collection_id => $genre_parent) {
            if (!sizeof(array_intersect(self::getCollectionLangs($collection_id), $lang_ids))) {
                continue;
            }            
            
            $collect_genres = [$genre_parent];
            foreach (Genre::whereIn('parent_id', $collect_genres)->get() as $g) {
                $collect_genres[] = $g->id;
            }
        
            if (!sizeof(array_intersect($collect_genres, $genre_ids))) {
                continue;
            }
            return $collection_id;
        }
    }
}