<?php

namespace App\Models\Corpus;

//use App\Models\Corpus\Author;
use App\Models\Corpus\Genre;
use App\Models\Dict\Lang;

class Collection
{

    public static function getCollectionGenres($collection_id = null)
    {
        $genres = [1 => 19, 2 => 66, 3 => 60, 6 => 19];
        if (!$collection_id) {
            return $genres;
        }
        if (isset($genres[$collection_id])) {
            return $genres[$collection_id];
        }
    }

    public static function getCollectionCorpuses($collection_id = null)
    {
        $corpuses = [9 => [1,15,4]];
        if (!$collection_id) {
            return $corpuses;
        }
        if (isset($corpuses[$collection_id])) {
            return $corpuses[$collection_id];
        }
    }

    public static function getCollectionAuthors($collection_id = null)
    {
        $authors = [4 => 335, 5 => 15, 8 => 131];
        if (!$collection_id) {
            return $authors;
        }
        if (!empty($authors[$collection_id])) {
            return $authors[$collection_id];
        }
    }

    public static function getCollectionLangs($collection_id = null): array
    {
        $langs = [1 => [1], 2 => [4, 5, 6], 3 => [4, 5, 6], 6 => [4, 5, 6], 9 => [4, 5, 6]];
        if (!$collection_id) {
            return $langs;
        }
        if (!empty($langs[$collection_id])) {
            return $langs[$collection_id];
        }
        return [];
    }

    public static function getCollectionIds()
    {
        return array_keys(trans('collection.name_list'));
    }

    public static function isCollectionbyAuthor($id): bool
    {
        $authors = self::getCollectionAuthors();
        if (!empty($authors[$id])) {
            return true;
        }
        return false;
    }

    public static function isCollectionbyGenre($id): bool
    {
        $genres = self::getCollectionGenres();
        if (!empty($genres[$id])) {
            return true;
        }
        return false;
    }

    public static function isCollectionbyCorpuses($id): bool
    {
        $corpuses = self::getCollectionCorpuses();
        if (!empty($corpuses[$id])) {
            return true;
        }
        return false;
    }

    public static function isCollectionId($id): bool
    {
        if (self::isCollectionbyAuthor($id)) {
            return true;
        } elseif (self::isCollectionbyGenre($id)) {
            return true;
        } elseif (self::isCollectionbyCorpuses($id)) {
            return true;
        } elseif ($id == 7) {
            return true;
        }
        return false;
    }

    public static function getCollectionId($lang_ids, $genre_ids, $author_ids = [])
    {
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

    /**
     * @var int $id - collection ID
     * @return array - [$dialects, $genres, $lang_ids, $langs, $text_count]
     */
    public static function getDataForCollectionByGenre(int $id) {
        $lang_ids = Collection::getCollectionLangs($id);
        $langs = Lang::whereIn('id', $lang_ids)->orderBy('id')->get();
        if ($id == 3) {
            $genre_arr = [Collection::getCollectionGenres($id)]; 
            $genres = [Genre::find($genre_arr[0])];
        } elseif ($id == 9)  {
            $genres = Genre::where(function ($q) use ($id) {
                    $q->whereIn('id', (array)Collection::getCollectionGenres($id))
                        ->orWhereIn('parent_id', (array)Collection::getCollectionGenres($id));
                })
                ->orderBy('sequence_number')->get(); // коллекция жанров и поджанров
            $genre_arr = $genres->pluck('id')->toArray();
        } else {
            $genres = Genre::where('parent_id', Collection::getCollectionGenres($id))
                ->orderBy('sequence_number')->get(); // коллекция поджанров
            $genre_arr = Genre::find(Collection::getCollectionGenres($id))
                ->getSubGenreIds();
        }
        $text_count = Text::whereIn('lang_id', $lang_ids)
            ->whereIn('id', function ($q) use ($genre_arr) {
                $q->select('text_id')->from('genre_text')
                    ->whereIn('genre_id', $genre_arr);
            })->count();
        $dialects = [];
        if ($id == 1) {
            $dialects = Dialect::whereIn('lang_id', $lang_ids)->get();
        } elseif ($id == 6) {
            foreach ($genres as $genre) {
                foreach ($langs as $lang) {
                    $dials = Dialect::where('lang_id', $lang->id)->get();
                    foreach ($dials as $dialect) {
                        $texts = $dialect->textsByGenre($genre->id)->sortBy('title');
                        if (!count($texts)) {
                            continue;
                        }
                        $dialects[$genre->id]['langs'][$lang->id]['dialects'][$dialect->id] = ['dialect' => $dialect, 'texts' => $texts];
                    }
                    $dialects[$genre->id]['langs'][$lang->id]['lang_text_count'] =
                        Text::where('lang_id', $lang->id)
                        ->whereIn('id', function ($q) use ($genre) {
                            $q->select('text_id')->from('genre_text')
                                ->where('genre_id', $genre->id);
                        })->count();
                }
                $dialects[$genre->id]['genre_text_count'] =
                    Text::whereIn('lang_id', $lang_ids)
                    ->whereIn('id', function ($q) use ($genre) {
                        $q->select('text_id')->from('genre_text')
                            ->where('genre_id', $genre->id);
                    })->count();
            }                                       
        }
        return [$dialects, $genres, $lang_ids, $langs, $text_count];
    }

    public static function getDataForCollectionByCorpuses(int $id) {
        $lang_ids = Collection::getCollectionLangs($id);
        $langs = Lang::whereIn('id', $lang_ids)->orderBy('id')->get();
        $corpuses = Corpus::whereIn('id', Collection::getCollectionCorpuses($id))
                    ->orderBy('name_'.LaravelLocalization::getCurrentLocale())->get();
dd($corpuses);                    
        $genre_arr = $genres->pluck('id')->toArray();
        $text_count = Text::whereIn('lang_id', $lang_ids)
            ->whereIn('id', function ($q) use ($genre_arr) {
                $q->select('text_id')->from('genre_text')
                    ->whereIn('genre_id', $genre_arr);
            })->count();
        $dialects = [];
        if ($id == 1) {
            $dialects = Dialect::whereIn('lang_id', $lang_ids)->get();
        } elseif ($id == 6) {
            foreach ($genres as $genre) {
                foreach ($langs as $lang) {
                    $dials = Dialect::where('lang_id', $lang->id)->get();
                    foreach ($dials as $dialect) {
                        $texts = $dialect->textsByGenre($genre->id)->sortBy('title');
                        if (!count($texts)) {
                            continue;
                        }
                        $dialects[$genre->id]['langs'][$lang->id]['dialects'][$dialect->id] = ['dialect' => $dialect, 'texts' => $texts];
                    }
                    $dialects[$genre->id]['langs'][$lang->id]['lang_text_count'] =
                        Text::where('lang_id', $lang->id)
                        ->whereIn('id', function ($q) use ($genre) {
                            $q->select('text_id')->from('genre_text')
                                ->where('genre_id', $genre->id);
                        })->count();
                }
                $dialects[$genre->id]['genre_text_count'] =
                    Text::whereIn('lang_id', $lang_ids)
                    ->whereIn('id', function ($q) use ($genre) {
                        $q->select('text_id')->from('genre_text')
                            ->where('genre_id', $genre->id);
                    })->count();
            }                                       
        }
        return [$corpuses, $text_count];
    }
}
