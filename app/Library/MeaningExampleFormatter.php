<?php

namespace App\Library;

class MeaningExampleFormatter
{
    /**
     * Формирует HTML с подсветкой совпадений и блоками распознанных слов.
     *
     * @return string
     */
    public static function highlight(array $item, array $word_blocks = [], array $window = [])
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

        $linked_ids = array_fill_keys($item['linked_w_ids'] ?? [], true);

        $xml = (string) $item['text_xml'];

        if (trim($xml) === '' || preg_match('/<!DOCTYPE|<!ENTITY/i', $xml)) {
            return self::escape($item['sentence']);
        }

        $xml = preg_replace('/^\s*<\?xml[^?]*\?>/u', '', $xml, 1);

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

        $word_context = [
            'dom_prefix' => !empty($item['example_id'])
                ? 'example_' . preg_replace(
                    '/[^0-9_]/',
                    '',
                    (string) $item['example_id']
                ) . '_'
                : '',
            'source_w_id' => (int) ($item['source_w_id'] ?? 0),
            'fragment_id' => (int) ($item['fragment_id'] ?? 0),
            'word_blocks' => $word_blocks,
            'original' => !empty($item['is_original']) ? 1 : 0
        ];
        
        $word_context['distances'] = [];
        $word_context['window'] = $window;

        $positions = $item['word_positions'] ?? [];
        $source_w_id = $word_context['source_w_id'];

        if (array_key_exists($source_w_id, $positions)) {
            $source_position = (int) $positions[$source_w_id];

            foreach ($positions as $w_id => $position) {
                $word_context['distances'][(int) $w_id] =
                    (int) $position - $source_position;
            }
        }

        if ($word_context['original']) {
            $word_context['dom_prefix'] = 'original_'
                . $word_context['dom_prefix'];
        }

        return self::renderNode(
            $document->documentElement,
            (int) $item['text_id'],
            $source_ids,
            $partner_ids,
            $linked_ids,
            $word_context
        );
    }

    /**
     * Сохраняет текст, добавляя подсветку и контейнеры штатных блоков ЛГП.
     */
    protected static function renderNode(\DOMNode $node, $text_id, array $source_ids, array $partner_ids, array $linked_ids, array $word_context)
    {
        if ($node->nodeType === XML_TEXT_NODE || $node->nodeType === XML_CDATA_SECTION_NODE) {
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
                $linked_ids,
                $word_context
            );
        }

        if ($node->localName !== 'w') {
            return $html;
        }

        $word_id = $node->getAttribute('id');
        $w_id = ctype_digit($word_id) ? (int) $word_id : 0;

        $distances = $word_context['distances'] ?? [];
        $window = $word_context['window'] ?? [];

        if (array_key_exists($w_id, $distances)) {
            $distance = (int) $distances[$w_id];

            $distance_label = $distance > 0
                ? '+' . $distance
                : (string) $distance;

            $position_classes = ['example-positioned-word'];

            if (
                $w_id !== $word_context['source_w_id']
                && isset($window['distance_from'], $window['distance_to'])
                && $distance >= (int) $window['distance_from']
                && $distance <= (int) $window['distance_to']
            ) {
                $position_classes[] = 'example-window-word';
            }

            $title = trans('meaning_examples.word_distance', [
                'distance' => $distance_label,
            ]);

            $html = '<span class="' . implode(' ', $position_classes) . '"'
                . ' data-word-distance="' . $distance . '"'
                . ' title="' . self::escape($title) . '">'
                . $html
                . '</span>';
        }
        
        $classes = [];

        if (isset($source_ids[$w_id])) {
            $classes[] = 'example-source';
        }

        if (isset($partner_ids[$w_id])) {
            $classes[] = 'example-partner';
        }

        if (isset($linked_ids[$w_id])) {
            $classes[] = 'word-linked';

            $word_key = $text_id . '_' . $w_id;
            $block_id = $word_context['dom_prefix'] . $word_key;

            $word_blocks = $word_context['word_blocks'] ?? [];
            $preloaded = array_key_exists($w_id, $word_blocks);

            $block_html = $preloaded
                ? $word_blocks[$w_id]
                : '<img class="img-loading"'
                . ' src="/images/waiting_small.gif" alt="">';

            return '<w id="' . $block_id . '"'
                . ' data-word-key="' . $word_key . '"'
                . ' data-source-w-id="' . $word_context['source_w_id'] . '"'
                . ' data-fragment-id="' . $word_context['fragment_id'] . '"'
                . ' data-original="' . $word_context['original'] . '"'
                . ' class="' . implode(' ', $classes) . '">'
                . $html
                . '<div id="links_' . $block_id . '"'
                . ' class="links-to-lemmas"'
                . ' data-downloaded="' . ($preloaded ? '1' : '0') . '">'
                . $block_html
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
