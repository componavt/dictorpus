<?php

namespace Tests;

use App\Models\Corpus\Word;

class WordMoveCharOutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Проверяет перенос символа в XML и нормализацию поля word.
     *
     * Настоящие сохранения и перенумерация не выполняются.
     *
     * @dataProvider provideWords
     *
     * @param int $langId Язык текста.
     * @param string $sourceWord Исходное написание без символа.
     * @param string $expectedWord Ожидаемая поисковая форма.
     * @return void
     */
    public function testMoveCharOutPreservesXmlSpellingAndNormalizesWord(
        $langId,
        $sourceWord,
        $expectedWord
    ) {
        $sourceXml = '<s id="7"><w id="61">#'
            . $sourceWord . '</w>.</s>';

        $expectedXml = '<s id="7">#<w id="61">'
            . $sourceWord . '</w>.</s>';

        $sentence = new WordMoveCharOutSentenceDouble($sourceXml);

        $word = $this->makeWord(
            '#' . $sourceWord,
            $langId,
            $sentence
        );

        $word->onSave = function () use (
            $word,
            $sentence,
            $expectedWord,
            $expectedXml
        ) {
            $this->assertSame($expectedWord, $word->word);
            $this->assertSame($expectedXml, $sentence->text_xml);

            $this->assertSame(
                [$expectedXml],
                $sentence->savedXml,
                'Предложение должно быть сохранено перед словом.'
            );

            $this->assertSame(
                [],
                $sentence->numberedXml,
                'Перенумерация должна происходить после сохранения слова.'
            );

            return true;
        };

        $this->expectOutputString(
            "<p>sentence_id=38822, word_id=9001<br>\n"
                . $expectedXml
                . "</p><br>\n"
        );

        $result = $word->moveCharOut('#');
        $this->assertSame(1, $word->saveCalls);
        $this->assertSame(0, $word->removeCalls);

        $this->assertNull($result);
        $this->assertSame($expectedWord, $word->word);
        $this->assertSame($expectedXml, $sentence->text_xml);
        $this->assertSame([$expectedXml], $sentence->savedXml);
        $this->assertSame([$expectedXml], $sentence->numberedXml);
    }

    /**
     * Ожидаемые формы заданы явно.
     *
     * @return array
     */
    public function provideWords()
    {
        return [
            'vepsian_case_and_apostrophe' => [
                1,
                'D’og',
                'dog',
            ],
            'language_five_converts_w' => [
                5,
                'tulow',
                'tulou',
            ],
            'language_two_keeps_w' => [
                2,
                'tulow',
                'tulow',
            ],
        ];
    }

    /**
     * Если поле word не начинается с символа, изменений быть не должно.
     *
     * @return void
     */
    public function testMoveCharOutDoesNothingWhenWordDoesNotStartWithChar()
    {
        $sourceXml = '<s id="7"><w id="61">#Kala</w>.</s>';
        $sentence = new WordMoveCharOutSentenceDouble($sourceXml);
        $word = $this->makeWord('kala', 1, $sentence);

        $this->expectOutputString('');

        $result = $word->moveCharOut('#');
        $this->assertSame(0, $word->saveCalls);
        $this->assertSame(0, $word->removeCalls);

        $this->assertNull($result);
        $this->assertSame('kala', $word->word);
        $this->assertSame($sourceXml, $sentence->text_xml);
        $this->assertSame([], $sentence->savedXml);
        $this->assertSame([], $sentence->numberedXml);
    }

    /**
     * Создаёт тестовую модель без конструктора Eloquent и загрузки связей из БД.
     *
     * @param string $storedWord Исходное значение word.
     * @param int $langId Язык текста.
     * @param WordMoveCharOutSentenceDouble $sentence Подмена предложения.
     * @return WordMoveCharOutSpy
     */
    private function makeWord(
        $storedWord,
        $langId,
        WordMoveCharOutSentenceDouble $sentence
    ) {
        $word = new WordMoveCharOutSpy();

        $word->setRawAttributes([
            'id' => 9001,
            'text_id' => 1509,
            'sentence_id' => 38822,
            's_id' => 7,
            'w_id' => 61,
            'word' => $storedWord,
        ], true);

        $text = new \stdClass();
        $text->lang_id = $langId;

        $word->setRelation('text', $text);
        $word->setRelation('sentence', $sentence);

        return $word;
    }
}

/**
 * Подмена предложения без Eloquent и доступа к БД.
 *
 * Запоминает XML в моменты сохранения и перенумерации,
 * но не выполняет соответствующие рабочие операции.
 */
class WordMoveCharOutSentenceDouble
{
    /**
     * @var string
     */
    public $text_xml;

    /**
     * @var array
     */
    public $savedXml = [];

    /**
     * @var array
     */
    public $numberedXml = [];

    /**
     * @param string $xml Исходный XML предложения.
     */
    public function __construct($xml)
    {
        $this->text_xml = $xml;
    }

    /**
     * Запоминает XML вместо сохранения в БД.
     *
     * @return bool
     */
    public function save()
    {
        $this->savedXml[] = $this->text_xml;

        return true;
    }

    /**
     * Запоминает XML вместо настоящей перенумерации.
     *
     * @return void
     */
    public function numerateWords()
    {
        $this->numberedXml[] = $this->text_xml;
    }
}

/**
 * Подменяет запись в БД, сохраняя возможность проверять порядок операций.
 */
class WordMoveCharOutSpy extends Word
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
