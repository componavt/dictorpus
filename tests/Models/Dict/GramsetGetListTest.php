<?php

namespace Tests;

use App\Models\Dict\Gramset;
use Illuminate\Database\Schema\Blueprint;

class GramsetGetListTest extends IsolatedSqliteTestCase
{
    public function testGetListKeepsOutputAndDoesNotGrowQueriesPerRow()
    {
        $connection = self::assertSqliteIsolation($this->app);
        $schema = $connection->getSchemaBuilder();

        $columns = [
            'gram_id_reflexive',
            'gram_id_infinitive',
            'gram_id_mood',
            'gram_id_tense',
            'gram_id_person',
            'gram_id_case',
            'gram_id_number',
            'gram_id_negation',
            'gram_id_voice',
            'gram_id_participle',
        ];

        $schema->create('grams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_short_ru');
            $table->string('name_short_en');
        });

        $schema->create('gramsets', function (Blueprint $table) use ($columns) {
            $table->increments('id');
            $table->integer('sequence_number');
            foreach ($columns as $column) {
                $table->unsignedInteger($column)->nullable();
            }
        });

        $schema->create('gramset_pos', function (Blueprint $table) {
            $table->unsignedInteger('gramset_id');
            $table->unsignedInteger('pos_id');
            $table->unsignedInteger('lang_id');
        });

        $attributes = [];
        $labels = [];
        foreach ($columns as $index => $column) {
            $id = $index + 1;
            $label = 'g' . $id;
            $connection->table('grams')->insert([
                'id' => $id,
                'name_short_ru' => $label,
                'name_short_en' => $label,
            ]);
            $attributes[$column] = $id;
            $labels[] = $label;
        }
        $text = implode(', ', $labels);

        $connection->table('gramsets')->insert(array_merge(
            $attributes,
            ['id' => 1, 'sequence_number' => 20]
        ));

        $connection->enableQueryLog();
        $connection->flushQueryLog();

        $this->assertSame([1 => $text], Gramset::getList(0));
        $singleCount = count($connection->getQueryLog());

        $connection->disableQueryLog();

        for ($id = 2; $id <= 20; $id++) {
            $connection->table('gramsets')->insert(array_merge(
                $attributes,
                ['id' => $id, 'sequence_number' => 21 - $id]
            ));
        }

        $expected = [];
        $numbered = [];
        for ($id = 20; $id >= 1; $id--) {
            $expected[$id] = $text;
            $numbered[$id] = (21 - $id) . '. ' . $text;
            $connection->table('gramset_pos')->insert([
                'gramset_id' => $id,
                'pos_id' => 5,
                'lang_id' => 1,
            ]);
        }

        $connection->enableQueryLog();
        $connection->flushQueryLog();

        $this->assertSame($expected, Gramset::getList(0));
        $multipleCount = count($connection->getQueryLog());

        $connection->disableQueryLog();

        $this->assertSame(11, $singleCount);
        $this->assertSame($singleCount, $multipleCount);

        $this->assertSame($expected, Gramset::getList(5, 1));
        $this->assertSame($expected, Gramset::getList(5));
        $this->assertSame($expected, Gramset::getList(0, 1));
        $this->assertSame($numbered, Gramset::getList(5, 1, true));
        $this->assertSame([], Gramset::getList(6, 1));
        $this->assertSame([], Gramset::getList(5, 2));
    }
}
