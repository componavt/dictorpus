<?php

namespace Tests;

use App\Library\Grammatic;
use App\Library\Grammatic\KarVerbOlo;

class OloCheckedParadigmsTest extends GrammarTestCase
{
    private static function snapshot()
    {
        $path = __DIR__ . '/../../Fixtures/olo_checked_paradigms.json';
        $data = json_decode(file_get_contents($path), true);
        if (!is_array($data) || !isset($data['lemmas'])) {
            throw new \RuntimeException('Invalid snapshot: ' . $path);
        }
        return $data['lemmas'];
    }

    public static function storedFormsProvider()
    {
        $cases = [];
        foreach (self::snapshot() as $lemma) {
            foreach ($lemma['forms'] as $id => $forms) {
                $key = $lemma['lemma'] . ' / dialect=44 / gramset=' . $id;
                $cases[$key] = [(int)$lemma['lemma_id'], (int)$id];
            }
        }
        return $cases;
    }

    private function variants($forms)
    {
        $out = [];
        foreach ((array)$forms as $form) {
            foreach (preg_split('/[,\/]\s*/u', $form) as $variant) {
                $variant = trim($variant);
                if ($variant !== '') {
                    $out[] = $variant;
                }
            }
        }
        $out = array_values(array_unique($out));
        sort($out, SORT_STRING);
        return $out;
    }

    public function testStoredParadigmByGramset($lemmaId, $gramsetId)
    {
        $lemma = null;

        foreach (self::snapshot() as $item) {
            if ((int)$item['lemma_id'] === $lemmaId) {
                $lemma = $item;
                break;
            }
        }

        if ($lemma === null) {
            $this->fail('Lemma not found in snapshot: ' . $lemmaId);
        }

        $expected = $lemma['forms'][$gramsetId];

        $message = $lemma['lemma'] . '; lemma_id=' . $lemma['lemma_id']
            . '; dialect=44; gramset_id=' . $gramsetId
            . '; ' . $lemma['gramset_names'][$gramsetId];

        if (!in_array($gramsetId, KarVerbOlo::getListForAutoComplete())) {
            $this->markTestSkipped($message . '; gramset not supported');
        }

        list($stems, $nameNum) = Grammatic::stemsFromTemplate(
            $lemma['template'],
            5,
            11,
            null,
            44,
            true
        );
        $this->assertTrue(is_array($stems), $message . '; template parsing failed');

        $actual = Grammatic::wordformByStems(
            5,
            11,
            44,
            $gramsetId,
            $stems,
            $nameNum,
            true
        );

        $this->assertEquals(
            $this->variants($expected),
            $this->variants($actual),
            $message
        );
    }
}
