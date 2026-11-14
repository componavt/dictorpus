<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;

class CorpusReferenceExport
{
    public static function corpora($filename)
    {
        $query = DB::connection('mysql_export')
            ->table('corpuses')
            ->select(
                'id as corpus_id',
                'name_en',
                'name_ru'
            )
            ->orderBy('id');

        MorphologyExport::writeQuery($filename, [
            'corpus_id',
            'name_en',
            'name_ru',
        ], $query);
    }

    public static function genres($filename)
    {
        $query = DB::connection('mysql_export')
            ->table('genres')
            ->select(
                'id as genre_id',
                'parent_id',
                'corpus_id',
                'sequence_number',
                'name_en',
                'name_ru',
                'name_pl_en',
                'name_pl_ru'
            )
            ->orderBy('id');

        MorphologyExport::writeQuery($filename, [
            'genre_id',
            'parent_id',
            'corpus_id',
            'sequence_number',
            'name_en',
            'name_ru',
            'name_pl_en',
            'name_pl_ru',
        ], $query);
    }

    public static function dialects($filename)
    {
        $query = DB::connection('mysql_export')
            ->table('dialects as d')
            ->leftJoin('langs as l', 'l.id', '=', 'd.lang_id')
            ->select(
                'd.id as dialect_id',
                'd.code as dialect_code',
                'l.code as lang_code',
                'd.sequence_number',
                'd.name_en',
                'd.name_ru'
            )
            ->orderBy('d.id');

        MorphologyExport::writeQuery($filename, [
            'dialect_id',
            'dialect_code',
            'lang_code',
            'sequence_number',
            'name_en',
            'name_ru',
        ], $query);
    }
}
