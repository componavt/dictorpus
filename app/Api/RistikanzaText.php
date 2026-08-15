<?php

namespace App\Api;

//use Illuminate\Support\Facades\Log;

use App\Models\Corpus\Genre;
use App\Models\Corpus\Place;
use App\Models\Corpus\Text;

class RistikanzaText
{
    const enabledCorpuses = [2, 4, 12, 15];

    public static function getTexts($url_args)
    {
        $texts = Text::search($url_args)
            ->with([
                'authors',
                'dialects',
                'lang',
                'transtext'
            ])
            ->paginate($url_args['limit_num']);

        /*Log::debug('Ristikanza API locale', [
            'app_locale' => app()->getLocale(),
            'accept_language' => $request->header('Accept-Language'),
            'title' => $texts->first()->title ?? null,
        ]);*/

        $items = $texts->getCollection()
            ->map(function ($text) {
                return [
                    'id' => $text->id,
                    'author' => $text->authorsToString(),
                    'title' => $text->title,
                    'lang' => $text->lang ? $text->lang->name : '',
                    'dialect' => $text->dialectsToArray(),
                    'trans_author' => $text->transtext ? $text->transtext->authorsToString() : '',
                    'trans_title' => $text->transtext ? $text->transtext->title : '',
                ];
            })
            ->values()
            ->all();

        if (isset($url_args['search_corpus'][0])) {
            $url_args['search_corpus'] = $url_args['search_corpus'][0];
        } else {
            $url_args['search_corpus'] = null;
        }

        if (isset($url_args['search_genre'][0])) {
            $url_args['search_genre'] = $url_args['search_genre'][0];
            $genre = Genre::find($url_args['search_genre'][0]);
            if ($genre) {
                $url_args['genre_name'] = $genre->name_pl;
                $parent = Genre::find($genre->parent_id);
                if ($parent) {
                    $url_args['genre_name'] = $parent->name_pl . '. ' . $url_args['genre_name'];
                }
            }
        } else {
            $url_args['search_genre'] = null;
        }
        $url_args = remove_empty($url_args);

        return [
            'data' => $items,
            'url_args' => $url_args,
            'current_page' => $texts->currentPage(),
            'last_page' => $texts->lastPage(),
            'per_page' => $texts->perPage(),
            'total' => $texts->total()
        ];
    }

    public static function textData(Text $text): array
    {
        $lang_id = $text->lang_id;
        $locale = app()->getLocale();
        $authors = [];
        foreach ($text->authors as $author) {
            $name = $author->getNameByLang($lang_id);
            $authors[$author->id] = $name ? $name : $author->name;
        }

        $corpus = $text->corpuses()->whereIn('id', self::enabledCorpuses)->first();
        $corpus_id = $corpus ? $corpus->id : null;

        $informants = $recorders = [];
        $event_place = $event_date = '';
        if ($text->event) {
            foreach ($text->event->informants as $informant) {
                $informants[$informant->id] = $informant->informantString();
            }
            $recorders = $text->event->recorders()->pluck('name_' . $locale, 'id')->toArray();
            $event_place = $text->event->place->placeString();
            $event_date = $text->event->date;
        }

        $source = [];
        if ($text->source) {
            $source['book'] = $text->source->bookToString();
            if ($text->source->ieeh_archive_number1) {
                $source['number'] = '№' . $text->source->ieeh_archive_number1;
                if ($text->source->ieeh_archive_number2) {
                    $source['number'] .= '/' . $text->source->ieeh_archive_number2;
                }
            }
            $source['comment'] = $text->source->comment;
        }

        $cyrtext = $transtext = [];
        if ($text->cyrtext) {
            $cyrtext['title'] = $text->cyrtext->title;
            $cyrtext['text'] = $text->cyrtext->text_xml
                ? str_replace(
                    "<s id=\"",
                    "<s class=\"cyr_sentence\" id=\"cyrtext_s",
                    str_replace(
                        "<w id=\"",
                        "<w class=\"cyr_word\" id=\"cyr_w_",
                        mb_ereg_replace('[¦^]', '', $text->cyrtext->text_xml)
                    )
                )
                : nl2br(mb_ereg_replace('[¦^]', '', $text->cyrtext->text));
        }
        if ($text->transtext) {
            $transtext['title'] = $text->transtext->title;
            $transtext['authors'] = $text->transtext->authorsToString();
            $transtext['lang'] = $text->transtext->lang ? $text->transtext->lang->short : null;
            $transtext['text'] = $text->transtext->text_xml
                ? str_replace(
                    "<s id=\"",
                    "<s class=\"trans_sentence\" id=\"transtext_s",
                    mb_ereg_replace('[¦^]', '', $text->transtext->text_xml)
                )
                : nl2br(mb_ereg_replace('[¦^]', '', $text->transtext->text));
        }
        $original_text = str_replace(
            "<s id=\"",
            "<s class=\"sentence\" id=\"text_s",
            mb_ereg_replace('[¦^]', '', $text->textFromStructure())
        );

        $photos = [];
        foreach ($text->getMedia() as $photo) {
            $photos[] = [
                'src' => $photo->getUrl('thumb'),
                'big' => $photo->getUrl(''),
                'title' => $photo->name
            ];
        }

        $audiotexts = [];
        foreach ($text->audiotexts as $audiotext) {
            $audiotexts[] = $audiotext->url();
        }

        $celebration_places = [];
        foreach (Place::whereIn('id', $text->getCelebrationPlaces())->get() as $place) {
            $celebration_places[$place->id] = $place->placeString();
        }

        return [
            'id' => $text->id,
            'title' => $text->title,
            'lang' => $text->lang ? $text->lang->short : null,
            'dialects' => $text->dialects()->pluck('name_' . $locale, 'id')->toArray(),
            'authors' => $authors,
            'corpus_id' => $corpus_id,
            'genres' => $text->genres()->pluck('name_' . $locale, 'id')->toArray(),
            'plots' => $text->plots()->pluck('name_' . $locale, 'id')->toArray(),
            'topics' => $text->topics()->pluck('name_' . $locale, 'id')->toArray(),
            'cycles' => $text->cycles()->pluck('name_' . $locale, 'id')->toArray(),
            'motives' => $text->motives()->pluck('name_' . $locale, 'id')->toArray(),
            'celebration_places' => $celebration_places,
            'event_place' => $event_place,
            'event_date' => $event_date,
            'informants' => $informants,
            'recorders' => $recorders,
            'source' => $source,
            'mentioned_places' => $text->placesToString(),
            'comment' => $text->comment,
            'text' => $original_text,
            'transtext' => $transtext,
            'cyrtext' => $cyrtext,
            'photos' => $photos,
            'audiotexts' => $audiotexts,
        ];
    }
    
    protected static function idsFromUrlArgs(array $urlArgs, $key)
    {
        $ids = isset($urlArgs[$key]) ? $urlArgs[$key] : [];

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $ids = array_filter($ids, function ($id) {
            return is_numeric($id) && (int) $id > 0;
        });

        return array_values(array_unique(array_map('intval', $ids)));
    }

    public static function forMap(array $urlArgs = [])
    {
        $topicIds = self::idsFromUrlArgs($urlArgs, 'search_topic');
        $regionIds = self::idsFromUrlArgs($urlArgs, 'search_region');
        $districtIds = self::idsFromUrlArgs($urlArgs, 'search_district');

        $textsQuery = Text::whereIn('id', function ($q) {
            $q->select('text_id')->from('plot_text')
                ->where('plot_id', env('PLOT_CELEBRATION_ID'));
        });

        if (sizeof($topicIds)) {
            $textsQuery->whereIn('id', function ($query) use ($topicIds) {
                $query->select('text_id')
                    ->from('text_topic')
                    ->whereIn('topic_id', $topicIds);
            });
        }
        
        $texts = $textsQuery
            ->with([
                'event.informants',
                'places',
            ])
            ->orderBy('id')
            ->get();
        
        $text_places = [];
        foreach ($texts as $text) {
            foreach ($text->getCelebrationPlaces() as $place_id) {
                $text_places[$place_id][$text->id] = $text->title;
            }
        }
        
        if (!sizeof($text_places)) {
            return [];
        }

        $placesQuery = Place::whereIn('id', array_keys($text_places));
        
        if (sizeof($regionIds)) {
            $placesQuery->whereIn('region_id', $regionIds);
        }

        if (sizeof($districtIds)) {
            $placesQuery->whereIn('district_id', $districtIds);
        }

        $places = $placesQuery->get();

        
        $objs = [];
        foreach ($places as $place) {
            $lat = $place->latitude;
            $lon = $place->longitude;
            
            if ($lat == 0 || $lon == 0) {
                continue;
            }
            
            $key = $lat . '_' . $lon;

            if (!isset($objs[$key])) {
                $objs[$key] = [
                    'place' => $place->name,
                    'lat' => $lat,
                    'lon' => $lon,
                    'texts' => [],
                ];
            } else {
                $objs[$key]['place'] .= '; ' . $place->name;
            }

            $objs[$key]['texts'] = array_replace(
                $objs[$key]['texts'],
                $text_places[$place->id]
            );
        }

        ksort($objs);
        return $objs;
    }
}
