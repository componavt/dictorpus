<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Monument extends Model
{
    use \App\Traits\Search\MonumentSearch;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = false; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 999999; //Stop tracking revisions after 999999 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    protected $fillable = ['author', 'title', 'place', 'publ_date_from', 
        'publ_date_to', 'pages', 'bibl_descr', 'dialect_id', 
        'graphic_id', 'has_trans', 'volume', 'types', 'is_printed', 'is_full', 
        'dcopy_link', 'publ', 'study', 'archive', 'comment'];    

    protected $casts = [
        'publ_date_from' => 'date',
        'publ_date_to'   => 'date',
        'has_trans' => 'boolean',
        'is_printed' => 'boolean',
        'is_full' => 'boolean',
        'types' => 'array',
    ];
    
    
    public static function boot()
    {
        parent::boot();
    }

    // Scopes
//    use \App\Traits\Scopes\SearchByType;
    
    // Belongs To Relations
//    use \App\Traits\Relations\BelongsTo\Lang;
    use \App\Traits\Relations\BelongsTo\Dialect;

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Langs;
    
    public function getGraphicNameAttribute() {
        if (!empty($this->graphic_id) && !empty(trans('monument.graphic_values')[$this->graphic_id])) {
            return trans('monument.graphic_values')[$this->graphic_id];
        }
    }

    public function getTypesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getPublDateFromForFormAttribute() {
        return $this->publ_date_from ? $this->publ_date_from->format('m.Y') : null;
    }
    
    public function getPublDateToForFormAttribute() {
        return $this->publ_date_to ? $this->publ_date_to->format('m.Y') : null;
    }
    
    // Основной атрибут — с полными названиями
    public function getPublDateAttribute()
    {
        return $this->formatPublicationDate();
    }

    // Краткий атрибут — с сокращёнными названиями
    public function getPublDateBriefAttribute()
    {
        return $this->formatPublicationDate(true);
    }    
    
    public function formatPublicationDate($brief = false) {
        $from = $this->publ_date_from; // Carbon|null
        $to   = $this->publ_date_to;   // Carbon|null

        $months = trans('date.'.($brief ? 'mons' : 'months')); 

        $format = function ($date) use ($months) {
            if (!$date) { return null; }
            return $months[$date->month] . ' ' . $date->year;
        };

        $fromStr = $format($from);
        $toStr   = $format($to);

    // Отладка
/*    \Log::info([
        'from_year' => $from->year,
        'from_month' => $from->month,
        'to_year' => $to->year,
        'to_month' => $to->month,
        'is_full_century' => (
            $from->year === 1801 &&
            $to->year === 1900 &&
            $from->month === 1 &&
            $to->month === 12
        ),
        'centuryStart' => (floor(($from->year - 1) / 100) * 100) + 1,
        'centuryEnd'   => (floor(($from->year - 1) / 100) * 100) + 1 + 99
    ]);*/

        if (!$fromStr && !$toStr) { return null; }
        if ($fromStr && !$toStr)  { return $fromStr; }
        if (!$fromStr && $toStr)  { return $toStr; }
        if ($fromStr === $toStr)  { return $fromStr; }

        // Правило: весь один год
        if ($from && $to && $from->year === $to->year && $from->month === 1 && $to->month === 12) {
            return (string) $from->year;
        }

        // Правило: несколько полных лет → "1841–1842"
        if ($from && $to && $from->month === 1 && $to->month === 12) {

            // Проверяем, охватывает ли диапазон ЦЕЛЫЙ век
            $centuryStart = (int)(floor(($from->year - 1) / 100) * 100) + 1; // 1801
            $centuryEnd   = (int)$centuryStart + 99;                         // 1900

            if ( $from->year === $centuryStart && $to->year === $centuryEnd) {
                $century = floor(($from->year - 1) / 100) + 1;
                return $century . ' '.trans('date.'.($brief ? 'cen' : 'century'));
            }

            // Иначе — обычный диапазон годов
            return $from->year . '–' . $to->year;
        }
        return "$fromStr – $toStr";
    }
    
    public function setTypesAttribute($value)
    {
        $this->attributes['types'] = $value ? json_encode($value) : null;
    }

    public function setDialectIdAttribute($value)
    {
        $this->attributes['dialect_id'] = empty($value) ? null : $value;
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
                // Устанавливаем первый день месяца
                $date->day(1);
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
                // Устанавливаем последний день месяца
                $date->endOfMonth();
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

/*    public function typeValue():Array{
        $value = [];
        if ($this->types) {
            foreach ($this->types as type) {
                $value[] = $lang->id;
            }
        }
        return $value;
    }*/
    
    public function typesToString()
    {
        $list = [];
        $type_values = trans('monument.type_values');
        
        foreach ($this->types as $type_id) {
            $list[] = isset($type_values[$type_id]) ? $type_values[$type_id] : null;
        }
        return join(', ', $list);
    }
    
    public function storeAdditionInfo($data){
        $this->langs()->sync($data['langs']);
    }
}
