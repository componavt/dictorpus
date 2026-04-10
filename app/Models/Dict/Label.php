<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Library\Str;
use App\Models\Dict\Syntype;

class Label extends Model
{
    public $timestamps = false;
    protected $fillable = ['name_en', 'name_ru', 'short_en', 'short_ru', 'visible'];
    const OlodictLabel = 3;
    const ZaikovLabel = 5;
    const LDLLabel = 12;

    public function identifiableName()
    {
        return $this->name;
    }

    // Methods
    use \App\Traits\Methods\getNameAttribute;
    use \App\Traits\Methods\getShortAttribute;

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Lemmas;
    use \App\Traits\Relations\BelongsToMany\Meanings;

    public function syntypes()
    {
        return $this->belongsToMany(Syntype::class, 'label_syntype');
    }

    public static function checkedOloLemmas()
    {
        return DB::table('label_lemma')->whereLabelId(self::OlodictLabel)
            ->whereStatus(1)
            ->select('lemma_id');
    }

    public static function ldlLemmas()
    {
        return DB::table('label_lemma')->whereLabelId(self::LDLLabel)
            //                 ->whereStatus(1)
            ->select('lemma_id');
    }

    public function lemmaCount()
    {
        $label_id = $this->id;
        return Lemma::whereIn('id', function ($q1) use ($label_id) {
            $q1->select('lemma_id')->from('label_lemma')
                ->whereLabelId($label_id);
        })->orWhereIn('id', function ($q1) use ($label_id) {
            $q1->select('lemma_id')->from('meanings')
                ->whereIn('id', function ($q2) use ($label_id) {
                    $q2->select('meaning_id')->from('label_meaning')
                        ->whereLabelId($label_id);
                });
        })->count();
    }

    public static function getList()
    {
        $locale = LaravelLocalization::getCurrentLocale();

        return self::whereVisible(1)->orderBy('name_' . $locale)
            ->pluck('name_' . $locale, 'id')->toArray();
    }

    public static function store($data)
    {
        if (!$data['name_ru']) {
            return;
        }
        if (!$data['short_ru']) {
            $data['short_ru'] = $data['name_ru'];
        }
        if (!$data['name_en']) {
            $data['name_en'] = $data['short_en'] ? $data['short_en'] : $data['name_ru'];
        }
        if (!$data['short_en']) {
            $data['short_en'] = $data['name_en'];
        }
        $label = Label::create($data);
        return $label;
    }

    public static function urlArgs($request)
    {
        $url_args = Str::urlArgs($request) + [
            'search_name'     => $request->input('search_name'),
            'search_visible'     => $request->input('search_visible'),
        ];

        return $url_args;
    }

    public static function search(array $url_args)
    {
        $builder = self::orderBy('name_' . app()->getLocale());

        if (!empty($url_args['search_name'])) {
            $builder->where(function ($q) use ($url_args) {
                $q->where('name_ru', 'like', $url_args['search_name'])
                    ->orWhere('name_en', 'like', $url_args['search_name']);
            });
        }

        if ($url_args['search_visible'] === "0" || $url_args['search_visible'] === "1") {
            //dd($url_args['search_visible']);
            $builder->where('visible', $url_args['search_visible']);
        }
        //dd(to_sql($builder));
        return $builder;
    }
}
