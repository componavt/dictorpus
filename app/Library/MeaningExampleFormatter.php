<?php

namespace App\Library;

class MeaningExampleFormatter
{
    /**
     * Формирует HTML с подсветкой совпадений и блоками распознанных слов.
     *
     * @return string
     */
    public static function highlight(array $item)
    {
        $source_ids = [];
        $partner_ids = [];

        foreach ($item['matches'] as $match) {
            if (!empty($match['source_w_id'])) {
                $source_ids[(int) $match['source_w_id']] = true;
            }

            if (!empty($match['partner_w_id'])) {
                $partner_ids[(int) $match['partner_w_id']] = true;
            }
        }

        $linked_ids = array_fill_keys(
            $item['linked_w_ids'] ?? [],
            true
        );

        $xml = (string) $item['text_xml'];

        if (
            trim($xml) === ''
            || preg_match('/<!DOCTYPE|<!ENTITY/i', $xml)
        ) {
            return self::escape($item['sentence']);
        }

        $xml = preg_replace(
            '/^\s*<\?xml[^?]*\?>/u',
            '',
            $xml,
            1
        );

        $document = new \DOMDocument('1.0', 'UTF-8');

        $document->resolveExternals = false;
        $document->substituteEntities = false;

        $previous_errors = libxml_use_internal_errors(true);

        try {
            $loaded = $document->loadXML(
                '<?xml version="1.0" encoding="UTF-8"?>'
                    . '<example-root>' . $xml . '</example-root>',
                LIBXML_NONET
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous_errors);
        }

        if (!$loaded) {
            return self::escape($item['sentence']);
        }

        return self::renderNode(
            $document->documentElement,
            (int) $item['text_id'],
            $source_ids,
            $partner_ids,
            $linked_ids
        );
    }

    /**
     * Сохраняет текст, добавляя подсветку и контейнеры штатных блоков ЛГП.
     */
    protected static function renderNode(
        \DOMNode $node,
        $text_id,
        array $source_ids,
        array $partner_ids,
        array $linked_ids
    ) {
        if (
            $node->nodeType === XML_TEXT_NODE
            || $node->nodeType === XML_CDATA_SECTION_NODE
        ) {
            return self::escape($node->nodeValue);
        }

        if (!($node instanceof \DOMElement)) {
            return '';
        }

        if (strtolower($node->localName) === 'br') {
            return '<br>';
        }

        $html = '';

        foreach ($node->childNodes as $child) {
            $html .= self::renderNode(
                $child,
                $text_id,
                $source_ids,
                $partner_ids,
                $linked_ids
            );
        }

        if ($node->localName !== 'w') {
            return $html;
        }

        $word_id = $node->getAttribute('id');
        $w_id = ctype_digit($word_id) ? (int) $word_id : 0;

        $classes = [];

        if (isset($source_ids[$w_id])) {
            $classes[] = 'example-source';
        }

        if (isset($partner_ids[$w_id])) {
            $classes[] = 'example-partner';
        }

        if (isset($linked_ids[$w_id])) {
            $classes[] = 'word-linked';

            $block_id = $text_id . '_' . $w_id;

            return '<w id="' . $block_id . '" class="'
                . implode(' ', $classes) . '">'
                . $html
                . '<div id="links_' . $block_id . '"'
                . ' class="links-to-lemmas" data-downloaded="0">'
                . '<img class="img-loading"'
                . ' src="/images/waiting_small.gif" alt="">'
                . '</div>'
                . '</w>';
        }

        if (!$classes) {
            return $html;
        }

        return '<span class="' . implode(' ', $classes) . '">'
            . $html
            . '</span>';
    }

    /**
     * Экранирует текст перед включением в HTML.
     */
    protected static function escape($text)
    {
        return htmlspecialchars(
            (string) $text,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }
}
