<?php

namespace App\Traits\Select;

use \Venturecraft\Revisionable\Revision;

use App\Models\User;

trait MeaningSelect
{
    public static function lastCreated($limit = '')
    {
        $meanings = self::latest();
        if ($limit) {
            $meanings = $meanings->take($limit);
        }
        $meanings = $meanings->get();
        foreach ($meanings as $meaning) {
            $revision = Revision::where('revisionable_type', 'like', '%Meaning')
                ->where('key', 'created_at')
                ->where('revisionable_id', $meaning->id)
                ->latest()->first();
            if ($revision) {
                $meaning->user = User::getNameByID($revision->user_id);
            }
        }
        return $meanings;
    }

    /**
     * Группирует сочетаемости по категориям и сортирует слова внутри групп.
     *
     * Категории без классификации выводятся последними.
     * Использует предварительно загруженные связи сочетаемостей.
     *
     * @return array
     */
    public function collocationsByCategory()
    {
        $collocations = $this->collocations->sort(function ($left, $right) {
            $left_meaning = $left->collocateMeaning;
            $right_meaning = $right->collocateMeaning;

            $comparison = strcmp(
                mb_strtolower($left_meaning->lemma->lemma),
                mb_strtolower($right_meaning->lemma->lemma)
            );

            if ($comparison !== 0) {
                return $comparison;
            }

            return $left_meaning->meaning_n <=> $right_meaning->meaning_n;
        });

        $groups = [];

        foreach ($collocations as $collocation) {
            $category_id = $collocation->concept_category_id;
            $group_key = $category_id === null ? '__uncategorized__' : $category_id;

            if (!isset($groups[$group_key])) {
                $groups[$group_key] = [
                    'category' => $collocation->conceptCategory,
                    'collocations' => [],
                ];
            }

            $groups[$group_key]['collocations'][] = $collocation;
        }

        uasort($groups, function ($left, $right) {
            $left_category = $left['category'];
            $right_category = $right['category'];

            if ($left_category === null) {
                return $right_category === null ? 0 : 1;
            }

            if ($right_category === null) {
                return -1;
            }

            return strnatcmp((string) $left_category->id, (string) $right_category->id);
        });

        return array_values($groups);
    }
}
