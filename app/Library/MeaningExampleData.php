<?php

namespace App\Library;

use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MeaningExampleData
{
    protected $source_meaning_id;
    protected $locale;

    /**
     * Задаёт исходное значение и язык отображения переводов.
     */
    public function __construct($source_meaning_id, $locale = null)
    {
        $this->source_meaning_id = (int) $source_meaning_id;
        $this->locale = $locale ?: LaravelLocalization::getCurrentLocale();
    }

    /**
     * Добавляет карточки исходных употреблений к предложениям страницы.
     */
    public function prepare(array $items)
    {
        if (!$items) {
            return [];
        }

        $sentence_ids = [];
        $text_ids = [];
        $w_ids = [];

        foreach ($items as $item) {
            $sentence_ids[$item['sentence_id']] = (int) $item['sentence_id'];
            $text_ids[$item['text_id']] = (int) $item['text_id'];

            foreach ($item['matches'] as $match) {
                $w_ids[$match['source_w_id']] = (int) $match['source_w_id'];
            }
        }

        $fragments = $this->loadFragments(
            array_values($sentence_ids),
            array_values($w_ids)
        );

        $translations = $this->loadTranslations(
            array_values($sentence_ids),
            array_values($w_ids)
        );

        $text_titles = $this->loadTextTitles(array_values($text_ids));

        foreach ($items as $index => $item) {
            $items[$index]['source_examples'] = $this->buildSourceExamples(
                $item,
                $fragments,
                $translations,
                $text_titles
            );
        }

        return $items;
    }

    /**
     * Загружает фрагменты по предложениям и целевым словам страницы.
     */
    protected function loadFragments(array $sentence_ids, array $w_ids)
    {
        $fragments = [];

        if (!$w_ids) {
            return $fragments;
        }

        $rows = DB::table('sentence_fragments')
            ->whereIn('sentence_id', $sentence_ids)
            ->whereIn('w_id', $w_ids)
            ->orderBy('id')
            ->get();

        foreach ($rows as $row) {
            $key = $row->sentence_id . ':' . $row->w_id;

            if (!isset($fragments[$key])) {
                $fragments[$key] = $row;
            }
        }

        return $fragments;
    }

    /**
     * Загружает переводы на язык интерфейса и русский язык.
     */
    protected function loadTranslations(array $sentence_ids, array $w_ids)
    {
        $translations = [];

        if (!$w_ids) {
            return $translations;
        }

        $rows = DB::table('sentence_translations as st')
            ->join('langs as l', 'l.id', '=', 'st.lang_id')
            ->whereIn('st.sentence_id', $sentence_ids)
            ->whereIn('st.w_id', $w_ids)
            ->whereIn('l.code', array_unique([$this->locale, 'ru']))
            ->select([
                'st.sentence_id',
                'st.w_id',
                'st.text',
                'l.code as lang_code',
            ])
            ->get();

        foreach ($rows as $row) {
            $key = $row->sentence_id . ':' . $row->w_id;

            $translations[$key][$row->lang_code] = (string) $row->text;
        }

        return $translations;
    }

    /**
     * Загружает названия текстов выбранной страницы.
     */
    protected function loadTextTitles(array $text_ids)
    {
        $titles = [];

        $rows = DB::table('texts')
            ->whereIn('id', $text_ids)
            ->select(['id', 'title'])
            ->get();

        foreach ($rows as $row) {
            $titles[$row->id] = $row->title;
        }

        return $titles;
    }

    /**
     * Объединяет пары с одним исходным употреблением в одну карточку.
     */
    protected function buildSourceExamples(
        array $item,
        array $fragments,
        array $translations,
        array $text_titles
    ) {
        $groups = [];

        foreach ($item['matches'] as $match) {
            $groups[$match['source_word_id']][] = $match;
        }

        $examples = [];

        foreach ($groups as $matches) {
            $examples[] = $this->buildSourceExample(
                $item,
                $matches,
                $fragments,
                $translations,
                $text_titles
            );
        }

        return $examples;
    }

    /**
     * Готовит текст, перевод и адрес оценки конкретного употребления.
     */
    protected function buildSourceExample(
        array $item,
        array $matches,
        array $fragments,
        array $translations,
        array $text_titles
    ) {
        $match = $matches[0];
        $source_w_id = (int) $match['source_w_id'];
        $key = $item['sentence_id'] . ':' . $source_w_id;

        $fragment = $fragments[$key] ?? null;

        $is_fragment = $fragment
            && trim((string) $fragment->text_xml) !== '';

        $translation_values = $translations[$key] ?? [];

        $translation_lang = null;
        $translation_text = '';

        foreach (array_unique([$this->locale, 'ru']) as $lang_code) {
            if (array_key_exists($lang_code, $translation_values)) {
                $translation_lang = $lang_code;
                $translation_text = $translation_values[$lang_code];
                break;
            }
        }

        $example = $item;

        unset($example['source_examples']);

        $example['example_id'] = $this->source_meaning_id
            . '_' . $item['text_id']
            . '_' . $item['s_id']
            . '_' . $source_w_id;

        $example['source_meaning_id'] = $this->source_meaning_id;
        $example['source_word_id'] = (int) $match['source_word_id'];
        $example['source_w_id'] = $source_w_id;
        $example['source_word'] = $match['source_word'];
        $example['source_position'] = (int) $match['source_position'];
        $example['source_relevance'] = (int) $match['source_relevance'];

        $example['matches'] = $matches;
        $example['match_count'] = count($matches);

        $example['original_xml'] = $item['text_xml'];

        $example['text_xml'] = $is_fragment
            ? (string) $fragment->text_xml
            : $item['text_xml'];

        $example['sentence'] = $this->plainText($example['text_xml']);
        $example['is_fragment'] = (bool) $is_fragment;
        $example['fragment_id'] = $is_fragment ? (int) $fragment->id : null;

        $example['editable_w_ids'] = $is_fragment
            ? [$source_w_id]
            : $item['linked_w_ids'];

        $example['translation_html'] = $translation_text !== ''
            ? process_text($translation_text)
            : '';

        $example['translation_lang'] = $translation_lang;
        $example['text_title'] = $text_titles[$item['text_id']] ?? '';

        $example['text_url'] = LaravelLocalization::localizeURL(
            '/corpus/text/' . $item['text_id']
                . '?search_sentence=' . $item['s_id']
        );

        return $example;
    }

    /**
     * Возвращает простой текст для диагностического вывода и запасного показа.
     */
    protected function plainText($xml)
    {
        return trim(preg_replace(
            '/\s+/u',
            ' ',
            strip_tags((string) $xml)
        ));
    }
}
