<?php namespace App\Traits\Search;

use App\Library\Str;

trait MonumentSearch
{ 
    // Methods
    use \App\Traits\Methods\search\strField;
    use \App\Traits\Methods\search\intField;
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_dialect' => (int)$request->input('search_dialect') ? (int)$request->input('search_dialect') : null,
                    'search_is_printed'     => (int)$request->input('search_is_printed') ? (int)$request->input('search_is_printed') : null,
                    'search_lang'     => (int)$request->input('search_lang') ? (int)$request->input('search_lang') : null,
                    'search_publ_date_from'     => (int)$request->input('search_publ_date_from') ? (int)$request->input('search_publ_date_from') : null,
                    'search_publ_date_to'     => (int)$request->input('search_publ_date_to') ? (int)$request->input('search_publ_date_to') : null,
                    'search_title' => $request->input('search_title'),
                    'search_type'     => (int)$request->input('search_type') ? (int)$request->input('search_type') : null,
                ];
        
        return $url_args;
    }    
    
    public static function search(Array $url_args) {
        $objs = self::orderBy('id', 'desc');

        $objs = self::searchIntField($objs, 'dialect_id', $url_args['search_dialect']);
        $objs = self::searchIntField($objs, 'is_printed', $url_args['search_is_printed']);
        $objs = self::searchByLang($objs, $url_args['search_lang']);
        $objs = self::searchByPublDate($objs, $url_args['search_publ_date_from'], $url_args['search_publ_date_to']);
        $objs = self::searchStrField($objs, 'title', $url_args['search_title']);
        $objs = self::searchByType($objs, $url_args['search_type']);
        
        return $objs;
    }    
    
    public static function searchByLang($objs, $lang_id) {
        if (empty($lang_id)) {
            return $objs;
        }
        $objs->whereIn('id', function ($q) use ($lang_id) {
            $q->select('monument_id')->from('lang_monument')
              ->whereLangId($lang_id);
        });
        return $objs;
    }    
    
    public static function searchByPublDate($objs, $fromYear, $toYear) {
        if ($fromYear) {
            $from = new \Carbon\Carbon("$fromYear-01-01");
            if ($from) {
                // Ищем памятники, у которых НАЧАЛО <= искомого КОНЦА
                // Но у нас диапазон — поэтому:
                $objs->where(function ($q) use ($from) {
                    // Вариант 1: publ_date_from известна → она >= $from
                    $q->where(function ($q2) use ($from) {
                        $q2->whereNotNull('publ_date_from')
                           ->whereDate('publ_date_from', '>=', $from->toDateString());
                    })
                    // Вариант 2: только publ_date_to известна → она >= $from
                    ->orWhere(function ($q2) use ($from) {
                        $q2->whereNull('publ_date_from')
                           ->whereNotNull('publ_date_to')
                           ->whereDate('publ_date_to', '>=', $from->toDateString());
                    });
                });
            }
        }

        if ($toYear) {
            $to = new \Carbon\Carbon("$toYear-12-31");
            if ($to) {
                $objs->where(function ($q) use ($to) {
                    $q->where(function ($q2) use ($to) {
                        $q2->whereNotNull('publ_date_to')
                           ->whereDate('publ_date_to', '<=', $to->toDateString());
                    })
                    ->orWhere(function ($q2) use ($to) {
                        $q2->whereNull('publ_date_to')
                           ->whereNotNull('publ_date_from')
                           ->whereDate('publ_date_from', '<=', $to->toDateString());
                    });
                });
            }

        }
//dd(to_sql($objs));        
        return $objs;
    }
    
    public static function searchByType($builder, $type_id) {
\Log::info('scopeSearchByType called with type_id: ' . ($type_id ?? 'null'));
        if (!$type_id) {
        // Отладка
        \Log::info('searchByType: type_id is empty, returning builder');
            return $builder;
        }
    \Log::info("searchByType: searching for type_id = {$type_id}");
        
        // Для MySQL 5.7+
        return $builder->whereRaw('JSON_CONTAINS(types, ?)', [(int)$type_id]);

        // Альтернатива для MySQL < 5.7 (работает, но менее надёжно):
        // return $builder->whereRaw('types LIKE ?', ['%"' . (int)$type_id . '"%']);
    }    
}