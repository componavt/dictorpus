<?php
namespace App\Models\Corpus;

//use App\Models\Corpus\Author;
use App\Models\Corpus\Genre;

class Collection {
   
    public static function getCollectionGenres($collection_id=null) {
        $genres = [1=>19, 2=>66, 3=>60, 6=>19];
        if (!$collection_id) {
            return $genres;
        }
        if (isset($genres[$collection_id])) {
            return $genres[$collection_id];
        }
    }

    public static function getCollectionAuthors($collection_id=null) {
        $authors = [4=>335, 5=>15];
        if (!$collection_id) {
            return $authors;
        }
        if (!empty($authors[$collection_id])) {
            return $authors[$collection_id];
        }
    }

    public static function getCollectionLangs($collection_id=null) : array {
        $langs = [1=>[1], 2=>[4,5,6], 3=>[4,5,6], 6=>[4,5,6]];
        if (!$collection_id) {
            return $langs;
        }
        if (!empty($langs[$collection_id])) {
            return $langs[$collection_id];
        }
        return [];
    }

    public static function getCollectionIds() {
        return array_keys(trans('collection.name_list'));
    }

    public static function isCollectionbyAuthor($id) : bool {
        $authors = self::getCollectionAuthors();
        if (!empty($authors[$id])) {
            return true;
        }
        return false;
    }
    
    public static function isCollectionbyGenre($id) : bool {
        $genres = self::getCollectionGenres();
        if (!empty($genres[$id])) {
            return true;
        }
        return false;
    }
    
    public static function isCollectionId($id) : bool {
        if (self::isCollectionbyAuthor($id)) {
            return true;
        } elseif (self::isCollectionbyGenre($id)) {
            return true;
        } elseif ($id==7) {
            return true;
        }
        return false;
    }
    
    public static function getCollectionId($lang_ids, $genre_ids, $author_ids=[]) {
        $lang_ids = (array)$lang_ids;
        
        foreach (self::getCollectionAuthors() as $collection_id => $author) {                    
            if (!in_array($author, $author_ids)) {
                continue;
            }
            return $collection_id;
        }
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