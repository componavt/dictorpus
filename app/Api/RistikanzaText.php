<?php

namespace App\Api;

use App\Models\Corpus\Text;

class RistikanzaText
{
    const enabledCorpuses = [2, 4, 12, 15];
    
    public static function getTexts($url_args) {
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
        
        $informants = $recorders = [];
        $event_place = $event_date = '';
        if ($text->event) {
            foreach ($text->event->informants as $informant) {
                $informants[$informant->id] = $informant->informantString();
            }
            $recorders = $text->event->recorders()->pluck('name_'.$locale,'id')->toArray();
            $event_place = $text->event->place->placeString();
            $event_date = $text->event->date;
        }
        
        $source = [];
        if ($text->source) {
            $source['book'] = $text->source->bookToString();
            if ($text->source->ieeh_archive_number1) {
                $source['number'] = '№'.$text->source->ieeh_archive_number1;
                if ($text->source->ieeh_archive_number2) {
                    $source['number'] .= '/'.$text->source->ieeh_archive_number2;
                }
            }
            $source['comment'] = $text->source->comment;           
        }
        
        $cyrtext = $transtext = [];
        if ($text->cyrtext) {
            $cyrtext['title'] = $text->cyrtext->title;
            $cyrtext['text'] = $text->cyrtext->text_xml 
                ? str_replace("<s id=\"", "<s class=\"cyr_sentence\" id=\"cyrtext_s",
                        str_replace("<w id=\"","<w class=\"cyr_word\" id=\"cyr_w_", 
                            mb_ereg_replace('[¦^]', '', $text->cyrtext->text_xml)))
                : nl2br(mb_ereg_replace('[¦^]', '', $text->cyrtext->text));
        }
        if ($text->transtext) {
            $transtext['title'] = $text->transtext->title;
            $transtext['authors'] = $text->transtext->authorsToString();
            $transtext['lang'] = $text->transtext->lang ? $text->transtext->lang->short : null;
            $transtext['text'] = $text->transtext->text_xml 
                ? str_replace("<s id=\"","<s class=\"trans_sentence\" id=\"transtext_s", 
                        mb_ereg_replace('[¦^]', '', $text->transtext->text_xml)) 
                : nl2br(mb_ereg_replace('[¦^]', '', $text->transtext->text));
        }
        $original_text = str_replace("<s id=\"","<s class=\"sentence\" id=\"text_s", 
                        mb_ereg_replace('[¦^]', '', $text->textFromStructure()));
        
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
        
        return [
            'id' => $text->id,
            'title' => $text->title,
            'lang' => $text->lang ? $text->lang->short : null,
            'dialects' => $text->dialects()->pluck('name_'.$locale,'id')->toArray(),
            'authors' => $authors,
            'corpuses' => $text->corpuses()->whereIn('id', self::enabledCorpuses)->pluck('name_'.$locale,'id')->toArray(),
            'genres' => $text->genres()->pluck('name_'.$locale,'id')->toArray(),
            'plots' => $text->plots()->pluck('name_'.$locale,'id')->toArray(),
            'topics' => $text->topics()->pluck('name_'.$locale,'id')->toArray(),
            'cycles' => $text->cycles()->pluck('name_'.$locale,'id')->toArray(),
            'motives' => $text->motives()->pluck('name_'.$locale,'id')->toArray(),
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

}
