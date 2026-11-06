<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;

class MorphologyExport
{
    public static function lemmas($lang_id, $filename)
    {
        $query = DB::connection('mysql_export')
            ->table('lemmas as l')
            ->leftJoin('langs as la', 'la.id', '=', 'l.lang_id')
            ->leftJoin('parts_of_speech as p', 'p.id', '=', 'l.pos_id')
            ->where('l.lang_id', $lang_id)
            ->select(
                'l.id as lemma_id',
                'l.lemma',
                'la.code as lang',
                'l.pos_id',
                'p.code as pos'
            )
            ->orderBy('l.id');

        self::writeQuery($filename, [
            'lemma_id',
            'lemma',
            'lang',
            'pos_id',
            'pos',
        ], $query);
    }

    public static function lemmaWordforms($lang_id, $filename)
    {
        $query = DB::connection('mysql_export')
            ->table('lemma_wordform as lw')
            ->join('lemmas as l', 'l.id', '=', 'lw.lemma_id')
            ->leftJoin('wordforms as w', 'w.id', '=', 'lw.wordform_id')
            ->leftJoin('dialects as d', 'd.id', '=', 'lw.dialect_id')
            ->where('l.lang_id', $lang_id)
            ->select(
                'lw.wordform_id',
                'w.wordform',
                'lw.wordform_for_search',
                'lw.lemma_id',
                'lw.gramset_id',
                'lw.dialect_id',
                'd.code as dialect_code',
                'lw.affix'
            )
            ->orderBy('lw.lemma_id')
            ->orderBy('lw.wordform_id')
            ->orderBy('lw.gramset_id')
            ->orderBy('lw.dialect_id')
            ->orderBy('lw.wordform_for_search')
            ->orderBy('lw.affix');

        self::writeQuery($filename, [
            'wordform_id',
            'wordform',
            'wordform_for_search',
            'lemma_id',
            'gramset_id',
            'dialect_id',
            'dialect_code',
            'affix',
        ], $query);
    }

    public static function grams($filename)
    {
        $query = DB::connection('mysql_export')
            ->table('grams as g')
            ->leftJoin(
                'gram_categories as c',
                'c.id',
                '=',
                'g.gram_category_id'
            )
            ->select(
                'g.id as gram_id',
                'g.gram_category_id',
                'c.name_en as category_en',
                'c.name_ru as category_ru',
                'g.name_en',
                'g.name_ru',
                'g.name_short_en',
                'g.name_short_ru',
                'g.lgr',
                'g.unimorph',
                'g.conll',
                'g.sequence_number'
            )
            ->orderBy('g.id');

        self::writeQuery($filename, [
            'gram_id',
            'gram_category_id',
            'category_en',
            'category_ru',
            'name_en',
            'name_ru',
            'name_short_en',
            'name_short_ru',
            'lgr',
            'unimorph',
            'conll',
            'sequence_number',
        ], $query);
    }

    public static function gramsets($filename)
    {
        $rows = collect(Export::get_gramset_by_id(true))
            ->sortBy('gramset_id');

        self::writeRows($filename, [
            'gramset_id',
            'gramset',
            'gram_id_number',
            'gram_id_case',
            'gram_id_tense',
            'gram_id_person',
            'gram_id_mood',
            'gram_id_negation',
            'gram_id_infinitive',
            'gram_id_voice',
            'gram_id_participle',
            'gram_id_reflexive',
        ], $rows);
    }

    public static function gramsetPos($filename)
    {
        $query = DB::connection('mysql_export')
            ->table('gramset_pos as gp')
            ->leftJoin('langs as la', 'la.id', '=', 'gp.lang_id')
            ->select(
                'gp.gramset_id',
                'la.code as lang',
                'gp.pos_id'
            )
            ->orderBy('gp.gramset_id')
            ->orderBy('gp.lang_id')
            ->orderBy('gp.pos_id');

        self::writeQuery($filename, [
            'gramset_id',
            'lang',
            'pos_id',
        ], $query);
    }

    public static function partsOfSpeech($filename)
    {
        $query = DB::connection('mysql_export')
            ->table('parts_of_speech')
            ->select(
                'id as pos_id',
                'code as pos',
                'name_en',
                'name_ru'
            )
            ->orderBy('id');

        self::writeQuery($filename, [
            'pos_id',
            'pos',
            'name_en',
            'name_ru',
        ], $query);
    }

    protected static function writeQuery($filename, array $columns, $query)
    {
        self::writeRows($filename, $columns, $query->cursor());
    }

    protected static function writeRows($filename, array $columns, $rows)
    {
        $path = storage_path('app/public/' . $filename);
        $handle = fopen($path, 'wb');

        if ($handle === false) {
            throw new \RuntimeException(
                'Не удалось открыть CSV: ' . $filename
            );
        }

        try {
            Export::write_csv_row($handle, $columns);

            $rowNumber = 1;

            foreach ($rows as $row) {
                $rowNumber++;
                $values = [];

                foreach ($columns as $column) {
                    $value = $row->{$column};

                    if ($value !== null && (string) $value === '\\N') {
                        throw new \RuntimeException(
                            'Коллизия маркера NULL в '
                                . $filename
                                . ', строка '
                                . $rowNumber
                                . ', столбец '
                                . $column
                        );
                    }

                    $values[] = $value === null ? '\\N' : $value;
                }

                Export::write_csv_row($handle, $values);
            }

            if (!fflush($handle)) {
                throw new \RuntimeException(
                    'Не удалось завершить запись CSV: ' . $filename
                );
            }
        } finally {
            fclose($handle);
        }
    }
}
