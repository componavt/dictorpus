<?php

namespace Tests;

use App\Models\Dict\Gramset;
use Illuminate\Database\Schema\Blueprint;

class GramsetEagerLoadingTest extends IsolatedSqliteTestCase
{
    public function testGramRelationsAreLoadedWithoutAdditionalQueries()
    {
        $connection = self::assertSqliteIsolation($this->app);
        $schema = $connection->getSchemaBuilder();

        $relations = [
            'gramReflexive' => 'gram_id_reflexive',
            'gramInfinitive' => 'gram_id_infinitive',
            'gramMood' => 'gram_id_mood',
            'gramTense' => 'gram_id_tense',
            'gramPerson' => 'gram_id_person',
            'gramCase' => 'gram_id_case',
            'gramNumber' => 'gram_id_number',
            'gramNegation' => 'gram_id_negation',
            'gramVoice' => 'gram_id_voice',
            'gramParticiple' => 'gram_id_participle',
        ];

        $schema->create('grams', function (Blueprint $table) {
            $table->increments('id');
        });

        $schema->create('gramsets', function (Blueprint $table) use ($relations) {
            $table->increments('id');
            foreach ($relations as $column) {
                $table->unsignedInteger($column)->nullable();
            }
        });

        $connection->table('grams')->insert(['id' => 1]);

        $row = ['id' => 1];
        foreach ($relations as $column) {
            $row[$column] = 1;
        }
        $connection->table('gramsets')->insert($row);

        $gramset = Gramset::with(array_keys($relations))->findOrFail(1);

        $connection->enableQueryLog();
        $connection->flushQueryLog();

        foreach ($relations as $relation => $column) {
            $this->assertTrue($gramset->relationLoaded($relation), $relation);
            $this->assertSame(1, (int) $gramset->$relation->id, $relation);
        }

        $this->assertSame([], $connection->getQueryLog());
    }
}
