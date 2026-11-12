<?php

namespace Tests;

use App\Models\Corpus\Word;

class WordCheckWordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Проверяет нормализацию, результат метода и число сохранений.
     *
     * Настоящий checkWord() выполняется на частичном mock:
     * save() и remove() заменены, приложение Laravel не загружается.
     *
     * @dataProvider provideNonEmptyWords
     *
     * @param string $storedWord Исходное значение поля word.
     * @param string $inputWord Написание, передаваемое в checkWord().
     * @param int $langId Язык текста.
     * @param string $expectedWord Ожидаемое значение поля word.
     * @param bool|null $expectedResult Ожидаемый результат метода.
     * @param int $expectedSaves Ожидаемое число вызовов save().
     * @return void
     */
    public function testCheckWordNormalizesBeforeComparingAndSaving(
        $storedWord,
        $inputWord,
        $langId,
        $expectedWord,
        $expectedResult,
        $expectedSaves
    ) {
        $word = $this->makeWord($storedWord, $langId);

        $word->expects($this->never())
            ->method('remove');

        $word->expects($this->exactly($expectedSaves))
            ->method('save')
            ->will($this->returnCallback(function () use ($word, $expectedWord) {
                $this->assertSame(
                    $expectedWord,
                    $word->word,
                    'В save() должно поступать уже нормализованное слово.'
                );

                return true;
            }));

        $result = $word->checkWord($inputWord);

        $this->assertSame($expectedResult, $result);
        $this->assertSame($expectedWord, $word->word);
    }

    /**
     * Примеры сравнения и сохранения поисковой формы.
     *
     * Ожидаемые значения заданы явно, без вызова changeLetters().
     * Пример tulow для языка 5 взят из существующих GrammaticTest.
     *
     * @return array
     */
    public function provideNonEmptyWords()
    {
        return [
            'already_equal' => [
                'kala',
                'kala',
                1,
                'kala',
                true,
                0,
            ],
            'uppercase_matches_normalized_word' => [
                'kala',
                'Kala',
                1,
                'kala',
                true,
                0,
            ],
            'curly_apostrophe_matches_normalized_word' => [
                'dog',
                'D’og',
                1,
                'dog',
                true,
                0,
            ],
            'straight_apostrophe_matches_normalized_word' => [
                'dog',
                "D'og",
                1,
                'dog',
                true,
                0,
            ],
            'surrounding_spaces_do_not_cause_save' => [
                'kala',
                ' Kala ',
                1,
                'kala',
                true,
                0,
            ],
            'stored_uppercase_is_repaired' => [
                'Kala',
                'Kala',
                1,
                'kala',
                null,
                1,
            ],
            'different_word_is_saved_normalized' => [
                'other',
                'D’og',
                1,
                'dog',
                null,
                1,
            ],
            'language_five_matches_normalized_word' => [
                'tulou',
                'tulow',
                5,
                'tulou',
                true,
                0,
            ],
            'language_five_saves_normalized_word' => [
                'other',
                'tulow',
                5,
                'tulou',
                null,
                1,
            ],
            'language_two_keeps_w' => [
                'tulow',
                'tulow',
                2,
                'tulow',
                true,
                0,
            ],
        ];
    }

    /**
     * Проверяет прежнюю ветку удаления при пустом входе.
     *
     * Настоящий remove() не выполняется.
     *
     * @dataProvider provideEmptyWords
     *
     * @param string|null $inputWord Пустое входное значение.
     * @return void
     */
    public function testCheckWordRemovesOnEmptyInput($inputWord)
    {
        $word = $this->makeWord('kala', 1);

        $word->expects($this->never())
            ->method('save');

        $word->expects($this->once())
            ->method('remove');

        $result = $word->checkWord($inputWord);

        $this->assertNull($result);
        $this->assertSame('kala', $word->word);
    }

    /**
     * Пустые значения, явно предусмотренные контрактом метода.
     *
     * @return array
     */
    public function provideEmptyWords()
    {
        return [
            'null_input' => [null],
            'empty_string' => [''],
        ];
    }

    /**
     * Создаёт модель без конструктора, запросов и загрузки приложения.
     *
     * Связь text заранее заполнена объектом с единственным нужным
     * свойством lang_id, поэтому её загрузка из БД не требуется.
     *
     * @param string $storedWord Исходное значение поля word.
     * @param int $langId Язык текста.
     * @return \App\Models\Corpus\Word|\PHPUnit_Framework_MockObject_MockObject
     */
    private function makeWord($storedWord, $langId)
    {
        $word = $this->getMockBuilder(Word::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'remove'])
            ->getMock();

        $word->setRawAttributes([
            'word' => $storedWord,
        ], true);

        $text = new \stdClass();
        $text->lang_id = $langId;

        $word->setRelation('text', $text);

        return $word;
    }
}
