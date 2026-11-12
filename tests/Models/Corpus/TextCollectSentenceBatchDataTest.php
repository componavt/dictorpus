<?php

namespace Tests;

use App\Traits\Modify\TextModify;

class TextCollectSentenceBatchDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Проверяет язык, нормализацию, структуру строк и уникальность поиска.
     *
     * @dataProvider provideLanguages
     *
     * @param int $langId Язык текста.
     * @param string $expectedFirst Поисковая форма tulow.
     * @param string $expectedLast Поисковая форма hawgi.
     * @return void
     */
    public function testCollectUsesTextLanguage(
        $langId,
        $expectedFirst,
        $expectedLast
    ) {
        $subject = new TextCollectSentenceBatchDataSubject($langId);

        $xml = new \SimpleXMLElement(
            '<s id="7">'
                . '<w id="61"> tulow </w>'
                . '<w id="62">TULOW</w>'
                . '<w id="63">hawgi</w>'
                . '</s>'
        );
        $xmlBefore = $xml->asXML();

        $checked = ['marker' => 'checked-first-word'];

        $result = $subject->collectForTest([
            [
                'sentence' => (object) [
                    'id' => 38822,
                    's_id' => 7,
                ],
                'sxe' => $xml,
                'checked_words' => [
                    0 => $checked,
                ],
            ],
        ], 1509);

        $expected = [
            'words_rows' => [
                [
                    'text_id' => 1509,
                    's_id' => 7,
                    'w_id' => 61,
                    'word' => $expectedFirst,
                    'word_number' => 1,
                    'sentence_id' => 38822,
                ],
                [
                    'text_id' => 1509,
                    's_id' => 7,
                    'w_id' => 62,
                    'word' => $expectedFirst,
                    'word_number' => 2,
                    'sentence_id' => 38822,
                ],
                [
                    'text_id' => 1509,
                    's_id' => 7,
                    'w_id' => 63,
                    'word' => $expectedLast,
                    'word_number' => 3,
                    'sentence_id' => 38822,
                ],
            ],
            'word_for_search_by_s_id_w_id' => [
                7 => [
                    61 => $expectedFirst,
                    62 => $expectedFirst,
                    63 => $expectedLast,
                ],
            ],
            'checked_by_s_id_w_id' => [
                7 => [
                    61 => $checked,
                ],
            ],
            'words_for_search' => [
                $expectedFirst,
                $expectedLast,
            ],
        ];

        $this->assertSame($expected, $result);
        $this->assertSame(
            $xmlBefore,
            $xml->asXML(),
            'Сбор поисковых форм не должен изменять исходный XML.'
        );
    }

    /**
     * Ожидаемые формы заданы явно, без вызова changeLetters().
     *
     * @return array
     */
    public function provideLanguages()
    {
        return [
            'language_five_converts_w' => [5, 'tulou', 'haugi'],
            'language_two_keeps_w' => [2, 'tulow', 'hawgi'],
            'language_one_keeps_w' => [1, 'tulow', 'hawgi'],
        ];
    }

    /**
     * Проверяет сохранение позиции после пропущенных XML-слов.
     *
     * Пропущенные элементы не должны сдвигать checked_words
     * на другое слово или попадать в поисковые массивы.
     *
     * @return void
     */
    public function testSkippedWordsPreservePositionAndCheckedMapping()
    {
        $subject = new TextCollectSentenceBatchDataSubject(5);

        $xml = new \SimpleXMLElement(
            '<s id="8">'
                . '<w id="0">skip</w>'
                . '<w id="11">   </w>'
                . '<w id="12">tulow</w>'
                . '</s>'
        );

        $keptChecked = ['marker' => 'third-position'];

        $result = $subject->collectForTest([
            [
                'sentence' => (object) [
                    'id' => 38823,
                    's_id' => 8,
                ],
                'sxe' => $xml,
                'checked_words' => [
                    0 => ['marker' => 'invalid-id'],
                    1 => ['marker' => 'empty-word'],
                    2 => $keptChecked,
                ],
            ],
        ], 1509);

        $this->assertSame([
            'words_rows' => [
                [
                    'text_id' => 1509,
                    's_id' => 8,
                    'w_id' => 12,
                    'word' => 'tulou',
                    'word_number' => 3,
                    'sentence_id' => 38823,
                ],
            ],
            'word_for_search_by_s_id_w_id' => [
                8 => [
                    12 => 'tulou',
                ],
            ],
            'checked_by_s_id_w_id' => [
                8 => [
                    12 => $keptChecked,
                ],
            ],
            'words_for_search' => ['tulou'],
        ], $result);
    }

    /**
     * Проверяет структуру результата для пустой партии.
     *
     * @return void
     */
    public function testEmptyBatchReturnsEmptyCollections()
    {
        $subject = new TextCollectSentenceBatchDataSubject(5);

        $this->assertSame([
            'words_rows' => [],
            'word_for_search_by_s_id_w_id' => [],
            'checked_by_s_id_w_id' => [],
            'words_for_search' => [],
        ], $subject->collectForTest([], 1509));
    }
}

/**
 * Изолированный носитель настоящего трейта.
 *
 * Не наследуется от Eloquent и не загружает приложение Laravel.
 * Публичный псевдоним открывает только проверяемый метод.
 */
class TextCollectSentenceBatchDataSubject
{
    use TextModify {
        collectSentenceBatchData as public collectForTest;
    }

    /**
     * Язык тестируемого текста.
     *
     * @var int
     */
    public $lang_id;

    /**
     * @param int $langId Язык текста.
     */
    public function __construct($langId)
    {
        $this->lang_id = $langId;
    }
}
