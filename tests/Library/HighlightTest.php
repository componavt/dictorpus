<?php

namespace Tests;

class HighlightTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider highlightProvider */
    public function testHighlight($source, $query, $expected, $class)
    {
        $this->assertSame($expected, highlight($source, $query, $class));
    }

    public function highlightProvider()
    {
        return [
            'plain phrase' => [
                'три тысячи лет назад',
                'лет назад',
                'три тысячи <span class="search-word">лет назад</span>',
                'search-word'
            ],
            'phrase in paragraph' => [
                '<p>три тысячи лет назад</p>',
                'лет назад',
                '<p>три тысячи <span class="search-word">лет назад</span></p>',
                'search-word'
            ],
            'two word tags' => [
                '<s><w id="85">лет</w> <w id="86">назад</w></s>',
                'лет назад',
                '<s><w id="85"><span class="search-word">лет</span></w> <w id="86"><span class="search-word">назад</span></w></s>',
                'search-word'
            ],
            'three word tags' => [
                '<w id="84">тысячи</w> <w id="85">лет</w> <w id="86">назад</w>',
                'тысячи лет назад',
                '<w id="84"><span class="search-word">тысячи</span></w> <w id="85"><span class="search-word">лет</span></w> <w id="86"><span class="search-word">назад</span></w>',
                'search-word'
            ],
            'single word' => [
                '<w id="85">лет</w>',
                'лет',
                '<w id="85"><span class="search-word">лет</span></w>',
                'search-word'
            ],
            'separated words' => [
                'лет много назад',
                'лет назад',
                'лет много назад',
                'search-word'
            ],
            'wrong order' => [
                'назад лет',
                'лет назад',
                'назад лет',
                'search-word'
            ],
            'sentence boundary' => [
                '<s><w>лет</w> </s><s><w>назад</w></s>',
                'лет назад',
                '<s><w>лет</w> </s><s><w>назад</w></s>',
                'search-word'
            ],
            'paragraph boundary' => [
                '<p>лет </p><p>назад</p>',
                'лет назад',
                '<p>лет </p><p>назад</p>',
                'search-word'
            ],
            'line break boundary' => [
                'лет <br/>назад',
                'лет назад',
                'лет <br/>назад',
                'search-word'
            ],
            'attributes untouched' => [
                '<a title="лет назад">лет назад</a>',
                'лет назад',
                '<a title="лет назад"><span class="search-word">лет назад</span></a>',
                'search-word'
            ],
            'quoted greater than' => [
                '<a title="x > y">лет назад</a>',
                'лет назад',
                '<a title="x > y"><span class="search-word">лет назад</span></a>',
                'search-word'
            ],
            'regular expression characters' => [
                'C++ язык',
                'C++ язык',
                '<span class="search-word">C++ язык</span>',
                'search-word'
            ],
            'case and repeated matches' => [
                'ЛЕТ НАЗАД и лет назад',
                'лет назад',
                '<span class="search-word">ЛЕТ НАЗАД</span> и <span class="search-word">лет назад</span>',
                'search-word'
            ],
            'inline formatting' => [
                '<w>лет</w> <w><b>назад</b></w>',
                'лет назад',
                '<w><span class="search-word">лет</span></w> <w><b><span class="search-word">назад</span></b></w>',
                'search-word'
            ],
            'no invented whitespace' => [
                '<w>лет</w><w>назад</w>',
                'лет назад',
                '<w>лет</w><w>назад</w>',
                'search-word'
            ],
            'whitespace preserved' => [
                'лет   назад',
                'лет назад',
                '<span class="search-word">лет   назад</span>',
                'search-word'
            ],
            'newline preserved as space' => [
                '
лет
назад',
                'лет назад',
                ' <span class="search-word">лет назад</span>',
                'search-word'
            ],
            'empty query' => [
                'лет назад',
                '',
                'лет назад',
                'search-word'
            ],
            'blank query' => [
                'лет назад',
                '   ',
                'лет назад',
                'search-word'
            ],
            'null query' => [
                'лет назад',
                null,
                'лет назад',
                'search-word'
            ],
            'custom class' => [
                'лет назад',
                'лет назад',
                '<span class="found">лет назад</span>',
                'found'
            ],
            'script excluded' => [
                '<script>лет назад</script> лет назад',
                'лет назад',
                '<script>лет назад</script> <span class="search-word">лет назад</span>',
                'search-word'
            ],
            'style excluded' => [
                '<style>лет назад</style> лет назад',
                'лет назад',
                '<style>лет назад</style> <span class="search-word">лет назад</span>',
                'search-word'
            ],
            'comment excluded' => [
                '<!-- лет назад -->лет назад',
                'лет назад',
                '<!-- лет назад --><span class="search-word">лет назад</span>',
                'search-word'
            ],
            'popup excluded' => [
                '<w>лет<div class="links-to-lemmas"><p>чужой текст</p></div></w> <w>назад</w>',
                'лет назад',
                '<w><span class="search-word">лет</span><div class="links-to-lemmas"><p>чужой текст</p></div></w> <w><span class="search-word">назад</span></w>',
                'search-word'
            ],
            'nested popup excluded' => [
                '<w>лет<div class="links-to-lemmas"><div>лет назад</div></div></w> <w>назад</w>',
                'лет назад',
                '<w><span class="search-word">лет</span><div class="links-to-lemmas"><div>лет назад</div></div></w> <w><span class="search-word">назад</span></w>',
                'search-word'
            ],
            'popup not a phrase' => [
                '<w>лет<div class="links-to-lemmas">назад</div></w>',
                'лет назад',
                '<w>лет<div class="links-to-lemmas">назад</div></w>',
                'search-word'
            ],
            'whitespace outside spans' => [
                '<b>лет </b><i> назад</i>',
                'лет назад',
                '<b><span class="search-word">лет</span> </b><i> <span class="search-word">назад</span></i>',
                'search-word'
            ],
        ];
    }
}
