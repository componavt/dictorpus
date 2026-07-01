<?php

namespace App\Traits\Modify;

use Illuminate\Support\Facades\DB;

use App\Models\Corpus\Punct;
use App\Models\Corpus\Putype;
use App\Models\Corpus\Word;


trait SentencePunctModify
{
    protected static $putypeBySlug = null;
    protected static $putypeMatchers = null;

    public function fixPunctsAfterSplit(int $old_w_id, int $new_right_w_id)
    {
        if (!$old_w_id || !$new_right_w_id) {
            return 0;
        }

        return Punct::where('text_id', $this->text_id)
            ->where('s_id', $this->s_id)
            ->where('left_w_id', $old_w_id)
            ->update([
                'left_w_id' => $new_right_w_id
            ]);
    }

    public function buildPunctRows()
    {
        $rows = [];
        $words = $this->words()->orderBy('word_number')->get();

        if ($words->isEmpty()) {
            return $rows;
        }

        $chains = $this->getPunctChainsForWords($words);
        $pNumber = 1;

        foreach ($chains as $chain) {
            $parsed = self::parsePunctChain($chain['punct']);

            foreach ($parsed as $item) {
                if (empty($item['putype_id'])) {
                    continue;
                }

                $rows[] = [
                    'text_id'     => $this->text_id,
                    's_id'        => $this->s_id,
                    'sentence_id' => $this->id,
                    'p_number'    => $pNumber++,
                    'left_w_id'   => $chain['left_w_id'],
                    'right_w_id'  => $chain['right_w_id'],
                    'putype_id'   => $item['putype_id'],
                    'punct'       => $item['symbol'],
                ];
            }
        }

        return $rows;
    }
    protected function getPunctChainsForWords($words)
    {
        $chains = [];

        if (empty($this->text_xml)) {
            return $chains;
        }

        $xml = trim($this->text_xml);

        if ($xml === '') {
            return $chains;
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        $loaded = $dom->loadXML($xml, LIBXML_NOERROR | LIBXML_NOWARNING);

        libxml_clear_errors();

        if (!$loaded) {
            return $chains;
        }

        $root = $dom->documentElement;

        if (!$root || $root->nodeName !== 's') {
            return $chains;
        }

        $leftWordId = null;
        $buffer = '';

        foreach ($root->childNodes as $node) {
            if ($node->nodeType === XML_TEXT_NODE || $node->nodeType === XML_CDATA_SECTION_NODE) {
                $buffer .= $node->nodeValue;
                continue;
            }

            if ($node->nodeType === XML_ELEMENT_NODE && $node->nodeName === 'w') {
                $rightWordId = (int)$node->getAttribute('id');
                $punct = $this->normalizePunctChain($buffer);

                if ($punct !== '') {
                    $chains[] = [
                        'left_w_id' => $leftWordId,
                        'right_w_id' => $rightWordId,
                        'punct' => $punct,
                    ];
                }

                $leftWordId = $rightWordId;
                $buffer = '';
            }
        }

        $punct = $this->normalizePunctChain($buffer);

        if ($punct !== '') {
            $chains[] = [
                'left_w_id' => $leftWordId,
                'right_w_id' => null,
                'punct' => $punct,
            ];
        }

        return $chains;
    }

    protected function normalizePunctChain($text)
    {
        if ($text === null || $text === '') {
            return '';
        }

        $text = preg_replace('/\s+/u', '', $text);

        return trim($text);
    }

    protected static function loadPutypeMatchers()
    {
        if (self::$putypeMatchers !== null) {
            return;
        }

        $rows = Putype::select('id', 'slug', 'symbols')->get();

        self::$putypeBySlug = [];
        self::$putypeMatchers = [];

        foreach ($rows as $row) {
            self::$putypeBySlug[$row->slug] = [
                'id' => $row->id,
                'slug' => $row->slug,
            ];

            $symbols = json_decode($row->symbols, true) ?: [];

            foreach ($symbols as $symbol) {
                self::$putypeMatchers[] = [
                    'id' => $row->id,
                    'slug' => $row->slug,
                    'symbol' => $symbol,
                    'length' => mb_strlen($symbol),
                ];
            }
        }

        usort(self::$putypeMatchers, function ($a, $b) {
            return $b['length'] <=> $a['length'];
        });
    }

    protected static function parsePunctChain($chain)
    {
        self::loadPutypeMatchers();

        $result = [];
        $offset = 0;
        $chainLength = mb_strlen($chain);

        while ($offset < $chainLength) {
            $matched = false;

            foreach (self::$putypeMatchers as $matcher) {
                $symbol = $matcher['symbol'];
                $length = $matcher['length'];

                if (mb_substr($chain, $offset, $length) !== $symbol) {
                    continue;
                }

                $slug = $matcher['slug'];
                $putypeId = $matcher['id'];

                if (in_array($slug, ['quote_open', 'quote_close'], true)) {
                    $resolvedSlug = self::resolveQuoteSlug($chain, $offset, $length);

                    if ($resolvedSlug && isset(self::$putypeBySlug[$resolvedSlug])) {
                        $slug = $resolvedSlug;
                        $putypeId = self::$putypeBySlug[$resolvedSlug]['id'];
                    }
                }

                $result[] = [
                    'symbol' => $symbol,
                    'slug' => $slug,
                    'putype_id' => $putypeId,
                    'offset' => $offset,
                ];

                $offset += $length;
                $matched = true;
                break;
            }

            if (!$matched) {
                $result[] = [
                    'symbol' => mb_substr($chain, $offset, 1),
                    'slug' => null,
                    'putype_id' => null,
                    'offset' => $offset,
                ];

                $offset++;
            }
        }

        return $result;
    }

    protected static function resolveQuoteSlug($chain, $offset, $length)
    {
        $prev = $offset > 0 ? mb_substr($chain, $offset - 1, 1) : null;
        $next = ($offset + $length < mb_strlen($chain))
            ? mb_substr($chain, $offset + $length, 1)
            : null;

        if ($prev === null) {
            return 'quote_open';
        }

        if ($next === null) {
            return 'quote_close';
        }

        $openAfter = ['(', '[', '{', '«', '„', '“', '"', '‘', "'", '—', '–', '‒', '―', '/', ' '];
        $closeBefore = [',', '.', '!', '?', ':', ';', ')', ']', '}', '»', '”', '’', '"', "'"];

        if (in_array($prev, $openAfter, true)) {
            return 'quote_open';
        }

        if (in_array($next, $closeBefore, true)) {
            return 'quote_close';
        }

        return 'quote_close';
    }
}
