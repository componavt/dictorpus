<?php

namespace Tests;

use App\Models\Corpus\Word;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

class WordMergeWordsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Проверяет настоящее объединение двух XML-слов.
     *
     * Соединение полностью подменено: поиск удаляемого слова
     * возвращает пустой результат, реальные запросы не выполняются.
     *
     * @dataProvider provideWords
     *
     * @param int $langId Язык текста.
     * @param string $firstWord Первое исходное слово.
     * @param string $lastWord Последнее исходное слово.
     * @param string $storedLast Поисковая форма последнего слова.
     * @param string $expectedMerged Итоговая поисковая форма.
     * @return void
     */
    public function testMergePreservesXmlSpellingAndNormalizesWord(
        $langId,
        $firstWord,
        $lastWord,
        $storedLast,
        $expectedMerged
    ) {
        $connection = new WordMergeWordsConnectionSpy();

        $resolver = new ConnectionResolver([
            'isolated_merge_test' => $connection,
        ]);
        $resolver->setDefaultConnection('isolated_merge_test');

        $text = new WordMergeWordsTextDouble(
            $langId,
            '<s id="7">'
                . '<w id="61">' . $firstWord . '</w> '
                . '<w id="62">' . $lastWord . '</w>.'
                . '</s>'
        );

        $word = new WordMergeWordsSpy();

        $word->setRawAttributes([
            'id' => 9001,
            'text_id' => 1509,
            'sentence_id' => 38822,
            's_id' => 7,
            'w_id' => 62,
            'word' => $storedLast,
        ], true);

        $word->setRelation('text', $text);

        $word->onSave = function () use (
            $word,
            $text,
            $expectedMerged
        ) {
            $this->assertSame($expectedMerged, $word->word);

            $this->assertSame(
                [],
                $text->updatedXml,
                'Слово должно сохраняться до вызова updateXML().'
            );

            return true;
        };

        $oldResolver = Word::getConnectionResolver();
        $oldLibxmlMode = libxml_use_internal_errors();

        try {
            Word::setConnectionResolver($resolver);

            $result = $word->mergeWords([
                61 => $firstWord,
                62 => $lastWord,
            ]);

            $this->assertSame(1, $word->saveCalls);
            $this->assertSame(0, $word->removeCalls);

            $this->assertCount(1, $connection->selectCalls);
            $this->assertInternalType(
                'string',
                $connection->selectCalls[0]['query']
            );
            $this->assertSame(
                [1509, 61],
                $connection->selectCalls[0]['bindings']
            );
            $this->assertNull($result);
            $this->assertSame($expectedMerged, $word->word);
            $this->assertCount(1, $text->updatedXml);

            $xml = new \SimpleXMLElement($text->updatedXml[0]);

            $removedNodes = $xml->xpath('//w[@id="61"]');
            $mergedNodes = $xml->xpath('//w[@id="62"]');

            $this->assertCount(0, $removedNodes);
            $this->assertCount(1, $mergedNodes);

            $this->assertSame(
                $firstWord . ' ' . $lastWord,
                (string) $mergedNodes[0],
                'XML должен сохранять исходное написание объединённых слов.'
            );

            $this->assertSame(
                $text->updatedXml[0],
                $text->text_xml
            );
        } finally {
            if ($oldResolver === null) {
                Word::unsetConnectionResolver();
            } else {
                Word::setConnectionResolver($oldResolver);
            }

            libxml_use_internal_errors($oldLibxmlMode);
        }
    }

    /**
     * Ожидаемые поисковые формы заданы явно.
     *
     * @return array
     */
    public function provideWords()
    {
        return [
            'language_five_converts_both_words' => [
                5,
                'hawgi',
                'TULOW',
                'tulou',
                'haugi tulou',
            ],
            'vepsian_case_and_apostrophe' => [
                1,
                'Kala',
                'D’og',
                'dog',
                'kala dog',
            ],
        ];
    }
}

/**
 * Подмена текста без Eloquent, сохранений и пересчёта разметки.
 */
class WordMergeWordsTextDouble
{
    /**
     * @var int
     */
    public $lang_id;

    /**
     * @var string
     */
    public $text_xml;

    /**
     * @var array
     */
    public $updatedXml = [];

    /**
     * @param int $langId Язык текста.
     * @param string $xml Исходный XML.
     */
    public function __construct($langId, $xml)
    {
        $this->lang_id = $langId;
        $this->text_xml = $xml;
    }

    /**
     * Запоминает XML вместо выполнения настоящего updateXML().
     *
     * @param string $xml Обновлённый XML.
     * @return void
     */
    public function updateXML($xml)
    {
        $this->updatedXml[] = $xml;
        $this->text_xml = $xml;
    }
}

/**
 * Запоминает запрос поиска слова, не открывая соединение с БД.
 */
class WordMergeWordsConnectionSpy extends Connection
{
    public $selectCalls = [];

    public function __construct()
    {
        // Настоящее соединение не создаётся.
    }

    public function getQueryGrammar()
    {
        return new Grammar();
    }

    public function getPostProcessor()
    {
        return new Processor();
    }

    public function getName()
    {
        return 'isolated_merge_test';
    }

    public function select($query, $bindings = [], $useReadPdo = true)
    {
        $this->selectCalls[] = [
            'query' => $query,
            'bindings' => $bindings,
            'useReadPdo' => $useReadPdo,
        ];

        return [];
    }
}

/**
 * Выполняет настоящий mergeWords(), подменяя сохранение и удаление.
 */
class WordMergeWordsSpy extends Word
{
    public $saveCalls = 0;
    public $removeCalls = 0;
    public $onSave;

    public function __construct()
    {
        // Намеренно не вызываем конструктор Eloquent.
    }

    public function save(array $options = [])
    {
        ++$this->saveCalls;

        if ($this->onSave !== null) {
            call_user_func($this->onSave);
        }

        return true;
    }

    public function remove()
    {
        ++$this->removeCalls;
    }
}
