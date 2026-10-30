<?php

namespace App\Library;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Models\Corpus\Corpus;
use App\Models\Dict\Gramset;
use App\Models\Dict\Meaning;

class MeaningExampleFilterValues
{
    public function load(Meaning $source)
    {
        $lemma = $source->lemma;

        return [
            'source_gramsets' => Gramset::getList((int) $lemma->pos_id, (int) $lemma->lang_id),
            'corpuses' => $this->corpuses($lemma->lang_id),
        ];
    }

    protected function corpuses($lang_id)
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $rows = Corpus::whereIn('id', function ($query) use ($lang_id) {
            $query->select('ct.corpus_id')->from('corpus_text as ct')
                ->join('texts as t', 't.id', '=', 'ct.text_id')
                ->where('t.lang_id', $lang_id)->distinct();
        })->orderBy('name_' . $locale)->get();

        $values = [];
        foreach ($rows as $row) {
            $values[(int) $row->id] = $row->name;
        }

        return $values;
    }
}
