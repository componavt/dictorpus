<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use App\Models\Corpus\Sentence;

class BetweenPunctTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Schema::dropIfExists('puncts');
        Schema::dropIfExists('putypes');
        Schema::dropIfExists('words');
        Schema::dropIfExists('sentences');
        Schema::dropIfExists('texts');

        Schema::create('texts', function (Blueprint $table) {
            $table->integer('id')->primary();
        });

        Schema::create('sentences', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('text_id');
            $table->integer('s_id');
        });

        Schema::create('words', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('text_id');
            $table->integer('sentence_id');
            $table->integer('s_id');
            $table->integer('w_id');
            $table->integer('word_number');
        });

        Schema::create('putypes', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('slug');
        });

        Schema::create('puncts', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('text_id');
            $table->integer('s_id');
            $table->integer('left_w_id');
            $table->integer('putype_id');
        });
    }

    /**
     * @dataProvider betweenCases
     */
    public function test_between_punct_scenarios(
        array $wordsData,
        array $punctsData,
        \stdClass $row,
        array $wordConfig,
        bool $expected
    ) {

        DB::table('texts')->insert(['id' => 10]);
        DB::table('sentences')->insert([
            'id'      => 100,
            'text_id' => 10,
            's_id'    => 3,
        ]);

        DB::table('words')->insert($wordsData);

        DB::table('putypes')->insert([
            ['id' => 1, 'slug' => 'comma'],
            ['id' => 2, 'slug' => 'dash'],
        ]);

        if (!empty($punctsData)) {
            DB::table('puncts')->insert($punctsData);
        }

        $result = Sentence::checkBetweenPunctByRow($row, 2, $wordConfig);

        $this->assertSame($expected, $result);
    }

    public function betweenCases(): array
    {
        // общая база слов A,B,C
        $baseWords = [
            [
                'id'           => 1,
                'text_id'      => 10,
                'sentence_id'  => 100,
                's_id'         => 3,
                'w_id'         => 1,
                'word_number'  => 1,
            ],
            [
                'id'           => 2,
                'text_id'      => 10,
                'sentence_id'  => 100,
                's_id'         => 3,
                'w_id'         => 2,
                'word_number'  => 2,
            ],
            [
                'id'           => 3,
                'text_id'      => 10,
                'sentence_id'  => 100,
                's_id'         => 3,
                'w_id'         => 3,
                'word_number'  => 3,
            ],
        ];

        return [
            // 1. A -> C, есть запятая, require_any → true
            'require_any_with_comma_between' => [
                $baseWords,
                [
                    [
                        'id'        => 1,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 1,
                        'putype_id' => 1,
                    ],
                ],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 1,
                    'word_number2' => 3,
                ],
                [
                    'bt_mode'  => 'require_any',
                    'bt_types' => [],
                ],
                true,
            ],

            // 2. A -> C, нет знака, require_any → false
            'require_any_without_punct' => [
                $baseWords,
                [],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 1,
                    'word_number2' => 3,
                ],
                [
                    'bt_mode'  => 'require_any',
                    'bt_types' => [],
                ],
                false,
            ],

            // 3. A -> C, запятая есть, forbid_any → false
            'forbid_any_with_punct' => [
                $baseWords,
                [
                    [
                        'id'        => 1,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 1,
                        'putype_id' => 1,
                    ],
                ],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 1,
                    'word_number2' => 3,
                ],
                [
                    'bt_mode'  => 'forbid_any',
                    'bt_types' => [],
                ],
                false,
            ],

            // 4. C -> A (обратный порядок), запятая остаётся между, require_any → true
            'require_any_reverse_order' => [
                $baseWords,
                [
                    [
                        'id'        => 1,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 1,
                        'putype_id' => 1,
                    ],
                ],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 3, // C
                    'word_number2' => 1, // A
                ],
                [
                    'bt_mode'  => 'require_any',
                    'bt_types' => [],
                ],
                true,
            ],

            // 5. A -> C, знак другого типа, require_any + bt_types → false
            'require_any_wrong_type' => [
                $baseWords,
                [
                    [
                        'id'        => 1,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 1,
                        'putype_id' => 2, // не тот тип
                    ],
                ],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 1,
                    'word_number2' => 3,
                ],
                [
                    'bt_mode'  => 'require_any',
                    'bt_types' => ['comma'], // а putypeIdsBySlugs даст id = 1
                ],
                false,
            ],

            // 6. Между A и C два знака разных типов, bt_types=['comma'] → true (есть хотя бы один нужный)
            'require_any_with_multiple_types_one_match' => [
                $baseWords,
                [
                    [
                        'id'        => 1,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 1, // запятая после A
                        'putype_id' => 1, // comma
                    ],
                    [
                        'id'        => 2,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 2, // тире после B
                        'putype_id' => 2, // dash
                    ],
                ],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 1,
                    'word_number2' => 3,
                ],
                [
                    'bt_mode'  => 'require_any',
                    'bt_types' => ['comma'],
                ],
                true,
            ],

            // 7. Между A и C один знак, bt_types задаёт два типа ['comma','dash'], есть только comma → true
            'require_any_with_multiple_bt_types' => [
                $baseWords,
                [
                    [
                        'id'        => 1,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 1,
                        'putype_id' => 1, // только comma
                    ],
                ],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 1,
                    'word_number2' => 3,
                ],
                [
                    'bt_mode'  => 'require_any',
                    'bt_types' => ['comma', 'dash'],
                ],
                true,
            ],

            // 8. Обратный порядок C -> A с конкретным типом, bt_mode=require_any, bt_types=['comma'] → true
            'require_any_reverse_order_with_type' => [
                $baseWords,
                [
                    [
                        'id'        => 1,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 1,
                        'putype_id' => 1, // comma
                    ],
                ],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 3, // C
                    'word_number2' => 1, // A
                ],
                [
                    'bt_mode'  => 'require_any',
                    'bt_types' => ['comma'],
                ],
                true,
            ],

            // 9. Одинаковая позиция слов, require_any -> false
            'same_position_require_any' => [
                $baseWords,
                [],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 2,
                    'word_number2' => 2,
                ],
                [
                    'bt_mode'  => 'require_any',
                    'bt_types' => [],
                ],
                false,
            ],

            // 10. Одинаковая позиция слов, forbid_any -> true
            'same_position_forbid_any' => [
                $baseWords,
                [],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 2,
                    'word_number2' => 2,
                ],
                [
                    'bt_mode'  => 'forbid_any',
                    'bt_types' => [],
                ],
                true,
            ],

            // 11. Между словами есть знак, но он не входит в запрещённые bt_types -> true
            'forbid_any_with_non_matching_type' => [
                $baseWords,
                [
                    [
                        'id'        => 1,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 1,
                        'putype_id' => 2, // dash
                    ],
                ],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 1,
                    'word_number2' => 3,
                ],
                [
                    'bt_mode'  => 'forbid_any',
                    'bt_types' => ['comma'], // запрещаем только comma
                ],
                true,
            ],

            // 12. Между словами несколько знаков, один подходит под bt_types -> require_any = true
            'require_any_multiple_puncts_one_matching_type' => [
                $baseWords,
                [
                    [
                        'id'        => 1,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 1,
                        'putype_id' => 2, // dash
                    ],
                    [
                        'id'        => 2,
                        'text_id'   => 10,
                        's_id'      => 3,
                        'left_w_id' => 2,
                        'putype_id' => 1, // comma
                    ],
                ],
                (object) [
                    's1_id'        => 3,
                    'word_number1' => 1,
                    'word_number2' => 3,
                ],
                [
                    'bt_mode'  => 'require_any',
                    'bt_types' => ['comma'],
                ],
                true,
            ],
        ];
    }
}
