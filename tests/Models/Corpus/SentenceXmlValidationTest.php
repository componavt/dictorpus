<?php

namespace Tests;

use App\Library\SentenceXmlValidator;

class SentenceXmlValidationTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider invalidSentenceXmlProvider */
    public function testInvalidSentenceIsRejectedBeforeParserAndDatabase($xml, $message)
    {
        foreach ([false, true] as $without_check) {
            $previous_mode = libxml_use_internal_errors();
            SentenceXmlValidationSubject::$parser_calls = 0;
            $subject = new SentenceXmlValidationSubject();
            $sentence = (object) [
                'id' => 51467, 's_id' => 16,
                'text_xml' => '<s id="16"><w id="136">mua</w></s>',
            ];
            $result = $subject->updateMeaningAndWordformText($sentence, $xml, $without_check);
            $this->assertInternalType('string', $result);
            $this->assertContains($message, $result);
            $this->assertSame(0, SentenceXmlValidationSubject::$parser_calls);
            $this->assertSame($previous_mode, libxml_use_internal_errors());
        }
    }

    public function invalidSentenceXmlProvider()
    {
        return [
            'empty' => ['', 'пустым'],
            'whitespace' => [" \r\n\t", 'пустым'],
            'not a string' => [[], 'пустым'],
            'malformed' => ['<s id="16"><w id="136">mua</s>', 'Некорректный XML'],
            'bare ampersand' => ['<s id="16"><w id="136">a&b</w></s>', 'Некорректный XML'],
            'wrong root' => ['<text><s id="16"/></text>', 'Корневой элемент'],
            'two sentences' => ['<s id="16"/><s id="17"/>', 'Некорректный XML'],
            'wrong sentence number' => ['<s id="17"/>', 'Номер <s'],
            'missing sentence number' => ['<s/>', 'Номер <s'],
            'nested sentence' => ['<s id="16"><s id="17"/></s>', 'Вложенные предложения'],
            'missing word number' => ['<s id="16"><w>mua</w></s>', 'Invalid word id'],
            'zero word number' => ['<s id="16"><w id="0">mua</w></s>', 'Invalid word id'],
            'negative word number' => ['<s id="16"><w id="-1">mua</w></s>', 'Invalid word id'],
            'fractional word number' => ['<s id="16"><w id="1.5">mua</w></s>', 'Invalid word id'],
            'nonnumeric word number' => ['<s id="16"><w id="abc">mua</w></s>', 'Invalid word id'],
            'duplicate word number' => ['<s id="16"><w id="136">mua</w><w id="136">ilma</w></s>', 'Duplicate word id 136'],
            'empty word' => ['<s id="16"><w id="136"/></s>', 'не должно быть пустым'],
            'blank word' => ['<s id="16"><w id="136"> &#160; </w></s>', 'не должно быть пустым'],
            'nested word' => ['<s id="16"><w id="136">mua<w id="138">ilma</w></w></s>', 'вложенные элементы'],
            'markup inside word' => ['<s id="16"><w id="136"><b>mua</b></w></s>', 'вложенные элементы'],
            'script' => ['<s id="16"><script>alert(1)</script></s>', 'Недопустимый элемент'],
            'word handler' => ['<s id="16"><w id="136" onclick="alert(1)">mua</w></s>', 'Недопустимый атрибут'],
            'format attribute' => ['<s id="16"><b style="color:red">1</b></s>', 'Недопустимый атрибут'],
            'sentence attribute' => ['<s id="16" extra="1"/>', 'Недопустимый атрибут'],
            'namespace' => ['<s xmlns="urn:test" id="16"/>', 'Корневой элемент'],
            'unused namespace' => ['<s xmlns:x="urn:test" id="16"/>', 'Недопустимый атрибут'],
            'doctype' => ['<!DOCTYPE s SYSTEM "file:///never-read"><s id="16"/>', 'DOCTYPE'],
            'entity' => ['<!DOCTYPE s [<!ENTITY x "word">]><s id="16">&x;</s>', 'DOCTYPE'],
            'processing instruction' => ['<?example data?><s id="16"/>', 'инструкции обработки'],
            'comment' => ['<s id="16"><!-- note --></s>', 'Комментарии'],
            'nonempty break' => ['<s id="16"><br>text</br></s>', 'должен быть пустым'],
            'nul byte' => ["<s id=\"16\">\0</s>", 'UTF-8'],
            'invalid utf8' => ["<s id=\"16\">\xFF</s>", 'UTF-8'],
        ];
    }

    /** @dataProvider validSentenceXmlProvider */
    public function testValidSentenceIsAcceptedWithoutChangingInput($xml)
    {
        $original = $xml;
        $previous_mode = libxml_use_internal_errors();
        $this->assertNull(SentenceXmlValidator::error($xml, 16));
        $this->assertSame($original, $xml);
        $this->assertSame($previous_mode, libxml_use_internal_errors());
    }

    public function validSentenceXmlProvider()
    {
        return [
            'ordinary' => ['<s id="16"><w id="136">mua</w>, <w id="138">ilma</w>.</s>'],
            'out of order ids' => ['<s id="16"><w id="900">mua</w> <w id="138">ilma</w>.</s>'],
            'empty sentence' => ['<s id="16"/>'],
            'punctuation only' => ['<s id="16">...</s>'],
            'formatting outside words' => ['<s id="16"><b><w id="136">mua</w></b><sup>1</sup><br/></s>'],
            'entities' => ['<s id="16"><w id="136">a&amp;b</w> &lt; &#228; &#xE4;</s>'],
            'trailing carriage return' => ["<s id=\"16\"><w id=\"136\">mua</w></s>\r"],
            'utf8 declaration' => ['<?xml version="1.0" encoding="UTF-8"?><s id="16"><w id="136">mua</w></s>'],
        ];
    }
}

/** Любой вызов прежнего парсера означает, что защитная проверка пропустила ввод. */
class SentenceXmlValidationSubject
{
    use \App\Traits\Modify\TextModify;

    public $id = 1880;
    public $lang_id = 6;
    public static $parser_calls = 0;

    public static function toXML($xml, $id = null)
    {
        self::$parser_calls++;
        throw new \RuntimeException('Invalid XML reached the general parser.');
    }
}
