<?php

namespace App\Models\Corpus;

use InvalidArgumentException;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Models\Corpus\Author;
use App\Models\Corpus\Corpus;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Plot;
use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

class Collection
{
    public int $id;

    public function __construct(int $id)
    {
        if (!in_array($id, self::getCollectionIds(), true)) {
            throw new InvalidArgumentException("Unknown collection id: {$id}");
        }

        $this->id = $id;
    }

    public static function find(int $id): ?self
    {
        return in_array($id, self::getCollectionIds(), true)
            ? new self($id)
            : null;
    }

    public function getName(): ?string
    {
        $key = "collection.name_list.{$this->id}";
        $value = trans($key);

        return $value === $key ? null : $value;
    }

    public function getLangIds()
    {
        return Collection::getCollectionLangs($this->id);
    }

    public function getLangs()
    {
        $lang_ids = $this->getLangIds();
        if (empty($lang_ids)) {
            return null;
        }
        return Lang::whereIn('id', $lang_ids)->orderBy('id')->get();
    }

    public function getGenres()
    {
        $genre_ids = self::getCollectionGenres($this->id);
        if (empty($genre_ids)) {
            return null;
        }
        return Genre::whereIn('id', $genre_ids)->get();
    }

    public function getAuthors()
    {
        $author_ids = self::getCollectionAuthors($this->id);
        if (empty($author_ids)) {
            return null;
        }
        return Author::whereIn('id', $author_ids)->get();
    }

    public function getCorpuses()
    {
        $corpus_ids = self::getCollectionCorpuses($this->id);
        if (empty($corpus_ids)) {
            return null;
        }
        return Corpus::whereIn('id', $corpus_ids)->get();
    }

    public function getPlotIds()
    {
        return self::getCollectionPlots($this->id);
    }

    public function getPlots($corpus_id = null)
    {
        $plot_ids = $this->getPlotIds();
        if (empty($plot_ids)) {
            return null;
        }
        $plots = Plot::whereIn('id', $plot_ids);
        if (!empty($corpus_id)) {
            $plots->whereIn('id', function ($q1) use ($corpus_id) {
                $q1->select('plot_id')->from('plot_text')
                    ->whereIn('text_id', function ($q2) use ($corpus_id) {
                        $q2->select('text_id')->from('corpus_text')
                            ->where('corpus_id', $corpus_id);
                    });
            });
        }
        return $plots->get();
    }

    public function countTextsForCorpus(int $corpus_id, $plot_id = null)
    {
        $texts = Text::whereIn('lang_id', $this->getLangIds())
            ->whereIn('id', function ($q) use ($corpus_id) {
                $q->select('text_id')->from('corpus_text')
                    ->where('corpus_id', $corpus_id);
            });
        $plot_ids = $this->getPlotIds();
        if ($plot_id) {
            $plot_ids = [$plot_id];
        }
        if (!empty($plot_ids)) {
            $texts->whereIn('id', function ($q) use ($plot_ids) {
                $q->select('text_id')->from('plot_text')
                    ->whereIn('plot_id', $plot_ids);
            });
        }
        return $texts->count();
    }
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
        $corpuses = [9 => [1, 15, 4]];
        if (!$collection_id) {
            return $corpuses;
        }
        if (isset($corpuses[$collection_id])) {
            return $corpuses[$collection_id];
        }
    }

    public static function getCollectionPlots($collection_id = null)
    {
        $plots = [9 => [53, 84, 85, 86]];
        if (!$collection_id) {
            return $plots;
        }
        if (isset($plots[$collection_id])) {
            return $plots[$collection_id];
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
        return array_keys((array)trans('collection.name_list'));
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
    public static function getDataForCollectionByGenre(int $id)
    {
        $lang_ids = Collection::getCollectionLangs($id);
        $langs = Lang::whereIn('id', $lang_ids)->orderBy('id')->get();
        if ($id == 3) {
            $genre_arr = [Collection::getCollectionGenres($id)];
            $genres = [Genre::find($genre_arr[0])];
        } elseif ($id == 9) {
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

    public static function getDataForCollectionByCorpuses(int $id)
    {
        $lang_ids = Collection::getCollectionLangs($id);
        $langs = Lang::whereIn('id', $lang_ids)->orderBy('id')->get();
        $corpuses = Corpus::whereIn('id', Collection::getCollectionCorpuses($id))
            ->orderBy('name_' . LaravelLocalization::getCurrentLocale())->get();
        $corpus_ids = $corpuses->pluck('id')->toArray();
        $texts = Text::whereIn('lang_id', $lang_ids)
            ->whereIn('id', function ($q) use ($corpus_ids) {
                $q->select('text_id')->from('corpus_text')
                    ->whereIn('corpus_id', $corpus_ids);
            });
        $plot_ids = self::getCollectionPlots($id);
        if ($plot_ids) {
            $texts->whereIn('id', function ($q) use ($plot_ids) {
                $q->select('text_id')->from('plot_text')
                    ->whereIn('plot_id', $plot_ids);
            });
        }
        $text_count = $texts->count();
        return [$corpuses, $text_count];
    }
}
