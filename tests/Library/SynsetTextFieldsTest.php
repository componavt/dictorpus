<?php

namespace Tests\Library;

use App\Library\SynsetTextFields;
use Caxy\HtmlDiff\HtmlDiff;
use Caxy\HtmlDiff\HtmlDiffConfig;

class SynsetTextFieldsTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyEditorContentBecomesNull()
    {
        $values = [
            null,
            '',
            " \t\n",
            '<div><br></div>',
            '<p>&nbsp;</p>',
        ];

        foreach ($values as $value) {
            $this->assertNull(
                SynsetTextFields::clean($value),
                'Empty editor content must become null.'
            );
        }
    }

    public function testAllowedFormattingIsPreserved()
    {
        $html = '<div><strong>kaunis</strong> '
            . '<em>красивый</em></div>'
            . '<ul><li>Первый кандидат</li></ul>'
            . '<p><a href="https://example.org/source">'
            . 'Источник</a></p>';

        $result = SynsetTextFields::clean($html);

        $this->assertContains('<strong>kaunis</strong>', $result);
        $this->assertContains('<em>красивый</em>', $result);
        $this->assertContains('<ul>', $result);
        $this->assertContains('<li>Первый кандидат</li>', $result);
        $this->assertContains(
            'href="https://example.org/source"',
            $result
        );
    }

    public function testUnsafeMarkupIsRemoved()
    {
        $html = '<p onclick="alert(1)">Текст</p>'
            . '<script>alert(2)</script>'
            . '<a href="javascript:alert(3)">Ссылка</a>'
            . '<img src="x" onerror="alert(4)">'
            . '<iframe src="https://example.org"></iframe>';

        $result = SynsetTextFields::clean($html);
        $lower = strtolower($result);

        $this->assertContains('Текст', $result);
        $this->assertContains('Ссылка', $result);

        foreach (
            [
                '<script',
                'onclick',
                'javascript:',
                '<img',
                'onerror',
                '<iframe',
            ] as $forbidden
        ) {
            $this->assertNotContains($forbidden, $lower);
        }
    }

    public function testUnicodeAndEntitiesArePreserved()
    {
        $text = 'ä ö ü š ž č — русский текст & kaunis';

        $html = '<p>'
            . htmlspecialchars($text, ENT_QUOTES, 'UTF-8')
            . '</p>';

        $result = SynsetTextFields::clean($html);

        $this->assertSame(
            $text,
            html_entity_decode(
                strip_tags($result),
                ENT_QUOTES,
                'UTF-8'
            )
        );
    }

    public function testZeroIsNotEmpty()
    {
        $this->assertSame(
            '0',
            strip_tags(SynsetTextFields::clean('0'))
        );
    }

    public function testCleaningIsIdempotent()
    {
        $html = '<div><strong>kaunis</strong>'
            . '<br>Комментарий</div>';

        $clean = SynsetTextFields::clean($html);

        $this->assertSame(
            $clean,
            SynsetTextFields::clean($clean)
        );
    }

    public function testArrayIsRejected()
    {
        $this->setExpectedException(
            'InvalidArgumentException'
        );

        SynsetTextFields::clean(['unexpected']);
    }

    public function testHtmlDiffWorksWithCleanedText()
    {
        $cache_dir = sys_get_temp_dir()
            . '/dictorpus-html-diff-'
            . uniqid('', true);

        if (!mkdir($cache_dir, 0700)) {
            throw new \RuntimeException(
                'Could not create temporary diff cache.'
            );
        }

        try {
            $config = new HtmlDiffConfig();
            $config->setPurifierCacheLocation($cache_dir);

            $old = SynsetTextFields::clean(
                '<p>Старое слово</p>'
            );

            $new = SynsetTextFields::clean(
                '<p>Новое слово</p>'
            );

            $result = HtmlDiff::create(
                $old,
                $new,
                $config
            )->build();

            $this->assertContains('<del', $result);
            $this->assertContains('<ins', $result);
            $this->assertContains('слово', $result);
        } finally {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $cache_dir,
                    \FilesystemIterator::SKIP_DOTS
                ),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }

            rmdir($cache_dir);
        }
    }
}
