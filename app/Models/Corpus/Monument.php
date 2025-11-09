<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Library\Str;

class Monument extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = false; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 999999; //Stop tracking revisions after 999999 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    protected $fillable = ['author', 'title', 'place', 'publ_date_from', 
        'publ_date_to', 'pages', 'bibl_descr', 'lang_id', 'dialect_id', 
        'graphic_id', 'has_trans', 'volume', 'type_id', 'is_printed', 'is_full', 
        'dcopy_link', 'publ', 'study', 'archive', 'comment'];    

    protected $casts = [
        'publ_date_from' => 'date',
        'publ_date_to'   => 'date',
        'has_trans' => 'boolean',
        'is_printed' => 'boolean',
        'is_full' => 'boolean',
    ];
    
    
    public static function boot()
    {
        parent::boot();
    }

    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lang;
    use \App\Traits\Relations\BelongsTo\Dialect;
    
    // Methods
    use \App\Traits\Methods\search\strField;
    use \App\Traits\Methods\search\intField;
    
    public function getGraphicNameAttribute() {
        if (!empty($this->graphic_id) && !empty(trans('monument.graphic_values')[$this->graphic_id])) {
            return trans('monument.graphic_values')[$this->graphic_id];
        }
    }

    public function getTypeAttribute() {
        if (!empty($this->type_id) && !empty(trans('monument.type_values')[$this->type_id])) {
            return trans('monument.type_values')[$this->type_id];
        }
    }

    public function setDialectIdAttribute($value)
    {
        $this->attributes['dialect_id'] = empty($value) ? null : $value;
    }
    
    public function getPublDateFromForFormAttribute() {
        return $this->publ_date_from ? $this->publ_date_from->format('m.Y') : null;
    }
    
    public function getPublDateToForFormAttribute() {
        return $this->publ_date_to ? $this->publ_date_to->format('m.Y') : null;
    }
    
    public function getPublDateAttribute() {
        $from = $this->publ_date_from; // Carbon|null
        $to   = $this->publ_date_to;   // Carbon|null

        $format = function ($date) {
            if (!$date) {
                return null;
            }
            return $date->formatLocalized('%B %Y');
        };

        $fromStr = $format($from);
        $toStr   = $format($to);

        if (!$fromStr && !$toStr) {
            return null;
        }

        if ($fromStr && !$toStr) {
            return $fromStr;
        }

        if (!$fromStr && $toStr) {
            return $toStr;
        }

        if ($fromStr === $toStr) {
            return $fromStr;
        }

        if (
            $from && $to &&
            $from->year === $to->year &&
            $from->month === 1 &&
            $to->month === 12
        ) {
            return (string) $from->year;
        }

        return "$fromStr – $toStr";
    }
    
    public function setPublDateFromAttribute($value) {
        if (empty($value)) {
            $this->attributes['publ_date_from'] = null;
            return;
        }

        // Поддерживаем строку в формате 'мм.гггг'
        if (is_string($value) && preg_match('/^\d{1,2}\.\d{4}$/', $value)) {
            // Нормализуем: '6.2025' → '06.2025' для соответствия 'm.Y'
            $value = preg_replace('/^(\d)\.(\d{4})$/', '0$1.$2', $value);
            try {
                $date = Carbon::createFromFormat('m.Y', $value);
                // Laravel сам сохранит как Y-m-d благодаря касту 'date'
                $this->attributes['publ_date_from'] = $date;
            } catch (\Exception $e) {
                // Некорректная дата — сохраняем null или бросаем исключение
                $this->attributes['publ_date_from'] = null;
            }
        } else {
            // Передаём как есть — например, уже Carbon, timestamp, '2025-06-01' и т.д.
            $this->attributes['publ_date_from'] = $value;
        }
    }

    public function setPublDateToAttribute($value) {
        if (empty($value)) {
            $this->attributes['publ_date_to'] = null;
            return;
        }

        // Поддерживаем строку в формате 'мм.гггг'
        if (is_string($value) && preg_match('/^\d{1,2}\.\d{4}$/', $value)) {
            // Нормализуем: '6.2025' → '06.2025' для соответствия 'm.Y'
            $value = preg_replace('/^(\d)\.(\d{4})$/', '0$1.$2', $value);
            try {
                $date = Carbon::createFromFormat('m.Y', $value);
                // Laravel сам сохранит как Y-m-d благодаря касту 'date'
                $this->attributes['publ_date_to'] = $date;
            } catch (\Exception $e) {
                // Некорректная дата — сохраняем null или бросаем исключение
                $this->attributes['publ_date_to'] = null;
            }
        } else {
            // Передаём как есть — например, уже Carbon, timestamp, '2025-06-01' и т.д.
            $this->attributes['publ_date_to'] = $value;
        }
    }
    
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
        $objs = self::searchIntField($objs, 'lang_id', $url_args['search_lang']);
        $objs = self::searchStrField($objs, 'title', $url_args['search_title']);
        $objs = self::searchIntField($objs, 'type_id', $url_args['search_type']);
        $objs = self::searchByPublDate($objs, $url_args['search_publ_date_from'], $url_args['search_publ_date_to']);
        
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
}
