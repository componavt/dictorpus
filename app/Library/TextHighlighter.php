<?php

namespace App\Library;

/** Подсветка фразы без пересборки исходной HTML/XML-разметки. */
class TextHighlighter
{
    public static function highlight(string $str, $substr = null, $class = 'search-word')
    {
        $str = preg_replace('/\R/u', ' ', $str);
        if ($substr === null || $substr === '') {
            return $str;
        }
        $needles = preg_split('/\s+/u', trim($substr), -1, PREG_SPLIT_NO_EMPTY);
        if (!$needles) {
            return $str;
        }
        $pattern = '/' . implode('\s+', array_map(function ($word) {
            return preg_quote($word, '/');
        }, $needles)) . '/iu';
        $parts = preg_split(
            '~(<!--.*?-->|<(?:"[^"]*"|\'[^\']*\'|[^\'">])*>)~su',
            $str,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
        if ($parts === false) {
            return $str;
        }

        $regions = [['text' => '', 'pieces' => []]];
        $region = 0;
        $skip_tag = null;
        $skip_depth = 0;
        $block_tags = [
            's', 'p', 'div', 'br', 'hr', 'li', 'ul', 'ol', 'dl', 'dt', 'dd',
            'table', 'thead', 'tbody', 'tfoot', 'tr', 'td', 'th', 'section',
            'article', 'header', 'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'blockquote', 'pre', 'script', 'style', 'textarea', 'title',
        ];

        foreach ($parts as $index => $part) {
            $is_tag = preg_match('~^<.*>$~s', $part);
            $tag = null;
            $closing = false;
            $self_closing = false;
            if ($is_tag && preg_match('~^<\s*(/?)\s*([a-z][a-z0-9:_-]*)\b~i', $part, $tag_match)) {
                $tag = strtolower($tag_match[2]);
                $closing = $tag_match[1] === '/';
                $self_closing = (bool) preg_match('~/\s*>$~', $part);
            }

            if ($skip_tag !== null) {
                if ($tag === $skip_tag) {
                    if ($closing) {
                        $skip_depth--;
                    } elseif (!$self_closing) {
                        $skip_depth++;
                    }
                    if ($skip_depth === 0) {
                        $skip_tag = null;
                    }
                }
                continue;
            }

            if ($is_tag) {
                $is_popup = false;
                if ($tag === 'div' && !$closing
                    && preg_match('~\sclass\s*=\s*(["\'])(.*?)\1~is', $part, $class_match)) {
                    $is_popup = (bool) preg_match('~(?:^|\s)links-to-lemmas(?:\s|$)~', $class_match[2]);
                }
                if ($is_popup) {
                    // Всплывающие блоки не являются частью строки предложения.
                    if (!$self_closing) {
                        $skip_tag = $tag;
                        $skip_depth = 1;
                    }
                    continue;
                }
                if (in_array($tag, $block_tags, true)) {
                    $regions[] = ['text' => '', 'pieces' => []];
                    $region++;
                }
                if (!$closing && !$self_closing
                    && in_array($tag, ['script', 'style', 'textarea', 'title'], true)) {
                    $skip_tag = $tag;
                    $skip_depth = 1;
                }
                continue;
            }

            $start = strlen($regions[$region]['text']);
            $regions[$region]['text'] .= $part;
            $regions[$region]['pieces'][] = [
                'index' => $index, 'start' => $start, 'end' => $start + strlen($part),
            ];
        }

        $ranges = [];
        foreach ($regions as $item) {
            if (!preg_match_all($pattern, $item['text'], $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }
            $cursor = 0;
            $pieces = $item['pieces'];
            $piece_count = count($pieces);
            foreach ($matches[0] as $match) {
                $match_start = $match[1];
                $match_end = $match_start + strlen($match[0]);
                while ($cursor < $piece_count && $pieces[$cursor]['end'] <= $match_start) {
                    $cursor++;
                }
                for ($i = $cursor; $i < $piece_count && $pieces[$i]['start'] < $match_end; $i++) {
                    $piece = $pieces[$i];
                    $start = max($match_start, $piece['start']) - $piece['start'];
                    $end = min($match_end, $piece['end']) - $piece['start'];
                    $slice = substr($parts[$piece['index']], $start, $end - $start);
                    if (!preg_match('/\S(?:.*\S)?/su', $slice, $visible, PREG_OFFSET_CAPTURE)) {
                        continue;
                    }
                    $start += $visible[0][1];
                    $ranges[$piece['index']][] = [$start, strlen($visible[0][0])];
                }
            }
        }

        $open = '<span class="' . htmlspecialchars((string) $class, ENT_QUOTES, 'UTF-8') . '">';
        foreach ($ranges as $index => $part_ranges) {
            foreach (array_reverse($part_ranges) as $range) {
                list($start, $length) = $range;
                $part = $parts[$index];
                $parts[$index] = substr($part, 0, $start) . $open
                    . substr($part, $start, $length) . '</span>'
                    . substr($part, $start + $length);
            }
        }
        return implode('', $parts);
    }
}
