<?php namespace App\Traits\Select;

use \Venturecraft\Revisionable\Revision;

use App\Models\User;

trait TextHistory
{
    public static function lastCreated($limit='') {
        $texts = self::latest();
        if ($limit) {
            $texts = $texts->take($limit);
        }
        $texts = $texts->get();
        
        // Получаем id всех текстов
        $textIds = $texts->pluck('id')->all();

        // Получаем последние ревизии по созданию для всех текстов
        $revisions = Revision::where('revisionable_type', 'like', '%Text')
            ->where('key', 'created_at')
            ->whereIn('revisionable_id', $textIds)
            ->latest()
            ->get()
            ->unique('revisionable_id')
            ->keyBy('revisionable_id');

        // Получаем id пользователей, чтобы не дёргать по одному
        $userIds = $revisions->pluck('user_id')->unique()->all();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        // Назначаем user-имя каждому тексту
        foreach ($texts as $text) {
            $revision = $revisions->get($text->id);
            if ($revision) {
                $text->user = $users[$revision->user_id]->name ?? null;
            }
        }
        
        return $texts;
    }
    
    public static function lastUpdated($limit='',$is_grouped=0) {
        // Получаем ревизии одним запросом
        $revisions = Revision::where('revisionable_type', 'like', '%Text')
            ->where('key', 'updated_at')
            ->latest()
            ->get()
            ->unique('revisionable_id')  // берём только одну ревизию на каждый текст
            ->take($limit);

        // Собираем id текстов и пользователей
        $textIds = $revisions->pluck('revisionable_id')->all();
        $userIds = $revisions->pluck('user_id')->filter()->unique()->all();

        // Загружаем тексты и пользователей пачкой
        $texts = self::whereIn('id', $textIds)->get()->keyBy('id');
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $result = [];

        foreach ($revisions as $revision) {
            $text = $texts->get($revision->revisionable_id);
            if (!$text) {
                continue;
            }

            // Добавляем имя пользователя
            $text->user = $users[$revision->user_id]->name ?? null;

            if ($is_grouped) {
                $updated_date = $text->updated_at->formatLocalized(trans('main.date_format'));
                $result[$updated_date][] = $text;
            } else {
                $result[] = $text;
            }
        }

        return $result;
    }
    
    public function allHistory() {
        $all_history = $this->revisionHistory->filter(function ($item) {
                            return $item['key'] != 'updated_at' 
                                   && $item['key'] != 'text_xml'
                                   && $item['key'] != 'transtext_id'
                                   && $item['key'] != 'event_id'
                                   && $item['key'] != 'checked'
                                   && $item['key'] != 'text_structure'
                                   && $item['key'] != 'source_id';
                                 //&& !($item['key'] == 'reflexive' && $item['old_value'] == null && $item['new_value'] == 0);
                        });
        foreach ($all_history as $history) {
            $history->what_created = trans('history.text_accusative');
        }
 
        if ($this->transtext) {
            $transtext_history = $this->transtext->revisionHistory->filter(function ($item) {
                                return $item['key'] != 'text_xml';
                            });
            foreach ($transtext_history as $history) {
                    $history->what_created = trans('history.transtext_accusative');
                    $fieldName = $history->fieldName();
                    $history->field_name = trans('history.'.$fieldName.'_accusative')
                            . ' '. trans('history.transtext_genetiv');
                }
                $all_history = $all_history -> merge($transtext_history);
        }
        
        if ($this->event) {
            $event_history = $this->event->revisionHistory->filter(function ($item) {
                                return $item['key'] != 'text_xml';
                            });
            foreach ($event_history as $history) {
                    $fieldName = $history->fieldName();
                    $history->field_name = trans('history.'.$fieldName.'_accusative')
                            . ' '. trans('history.event_genetiv');
                }
                $all_history = $all_history -> merge($event_history);
        }
        
        if ($this->source) {
            $source_history = $this->source->revisionHistory->filter(function ($item) {
                                return $item['key'] != 'text_xml';
                            });
            foreach ($source_history as $history) {
                    $fieldName = $history->fieldName();
                    $history->field_name = trans('history.'.$fieldName.'_accusative')
                            . ' '. trans('history.source_genetiv');
                }
                $all_history = $all_history -> merge($source_history);
        }
         
        $all_history = $all_history->sortByDesc('id')
                      ->groupBy(function ($item, $key) {
                            return (string)$item['updated_at'];
                        });
//dd($all_history);                        
        return $all_history;
    }
    
}