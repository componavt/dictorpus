<?php

use Illuminate\Database\Seeder;

class SyntypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('syntypes')->insert(['id' => 1,
                                    'name_en' => 'full',
                                    'name_ru' => 'полные',
                                    'comment' => 'абсолютные синонимы, взаимозаменяемые во всех контекстах']);
        DB::table('syntypes')->insert(['id' => 2,
                                    'name_en' => 'partial',
                                    'name_ru' => 'частичные',
                                    'comment' => 'совпадают только в части контекстов']);
        DB::table('syntypes')->insert(['id' => 3,
                                    'name_en' => 'near-synonyms',
                                    'name_ru' => 'близкие',
                                    'comment' => 'перекрывающиеся значения, «почти синонимы»']);
        DB::table('syntypes')->insert(['id' => 4,
                                    'name_en' => 'stylistic',
                                    'name_ru' => 'стилистические',
                                    'comment' => 'различаются по стилю (книжный, разговорный, просторечный)']);
        DB::table('syntypes')->insert(['id' => 5,
                                    'name_en' => 'dialectal',
                                    'name_ru' => 'диалектные',
                                    'comment' => 'различаются по территории / варианту языка']);
        DB::table('syntypes')->insert(['id' => 6,
                                    'name_en' => 'archaic',
                                    'name_ru' => 'устаревшие/архаизмы',
                                    'comment' => 'различаются по времени употребления']);
        DB::table('syntypes')->insert(['id' => 7,
                                    'name_en' => 'terminological',
                                    'name_ru' => 'терминологические',
                                    'comment' => 'различаются по сфере (термин ↔ общеупотребительное)']);
        DB::table('syntypes')->insert(['id' => 8,
                                    'name_en' => 'expressive',
                                    'name_ru' => 'экспрессивные',
                                    'comment' => 'различаются по интенсивности/эмоции (усиление, смягчение, жаргон)']);
    }
}

