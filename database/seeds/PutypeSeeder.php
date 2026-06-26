<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PuTypeSeeder extends Seeder
{
    public function run()
    {
        $rows = [
            [
                'id' => 1,
                'slug' => 'comma',
                'name_en' => 'comma',
                'name_ru' => 'запятая',
                'symbols' => json_encode([','], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 2,
                'slug' => 'period',
                'name_en' => 'period',
                'name_ru' => 'точка',
                'symbols' => json_encode(['.'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 3,
                'slug' => 'ellipsis',
                'name_en' => 'ellipsis',
                'name_ru' => 'многоточие',
                'symbols' => json_encode(['...', '…'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 4,
                'slug' => 'colon',
                'name_en' => 'colon',
                'name_ru' => 'двоеточие',
                'symbols' => json_encode([':'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 5,
                'slug' => 'semicolon',
                'name_en' => 'semicolon',
                'name_ru' => 'точка с запятой',
                'symbols' => json_encode([';'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 6,
                'slug' => 'dash',
                'name_en' => 'dash',
                'name_ru' => 'тире',
                'symbols' => json_encode(['-', '–', '—'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 7,
                'slug' => 'question',
                'name_en' => 'question mark',
                'name_ru' => 'вопросительный знак',
                'symbols' => json_encode(['?'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 8,
                'slug' => 'exclamation',
                'name_en' => 'exclamation mark',
                'name_ru' => 'восклицательный знак',
                'symbols' => json_encode(['!'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 9,
                'slug' => 'question_exclamation',
                'name_en' => 'question-exclamation mark',
                'name_ru' => 'вопросительно-восклицательный знак',
                'symbols' => json_encode(['?!'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 10,
                'slug' => 'exclamation_question',
                'name_en' => 'exclamation-question mark',
                'name_ru' => 'восклицательно-вопросительный знак',
                'symbols' => json_encode(['!?'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 11,
                'slug' => 'quote_open',
                'name_en' => 'opening quote',
                'name_ru' => 'открывающая кавычка',
                'symbols' => json_encode(['«', '„', '“', '"', '‘', "'"], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 12,
                'slug' => 'quote_close',
                'name_en' => 'closing quote',
                'name_ru' => 'закрывающая кавычка',
                'symbols' => json_encode(['»', '”', '“', '"', '’', "'"], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 13,
                'slug' => 'bracket_open',
                'name_en' => 'opening bracket',
                'name_ru' => 'открывающая скобка',
                'symbols' => json_encode(['(', '[', '{'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'id' => 14,
                'slug' => 'bracket_close',
                'name_en' => 'closing bracket',
                'name_ru' => 'закрывающая скобка',
                'symbols' => json_encode([')', ']', '}'], JSON_UNESCAPED_UNICODE),
            ],
        ];

        foreach ($rows as $row) {
            DB::table('putypes')->updateOrInsert(
                ['id' => $row['id']],
                $row
            );
        }
    }
}