<?php

namespace App\Library;

use App\Models\Dict\Meaning;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MeaningCollocationCandidateLinks
{
    public function prepare(Meaning $source, array $groups, array $filters)
    {
        $root = '/dict/meaning/' . $source->id;
        $return_path = $root . '/examples/candidates?' . http_build_query($filters);
        $context = ['examples_return' => $return_path];

        if (isset($filters['synset_id'])) {
            $context['synset_id'] = $filters['synset_id'];
        }

        foreach ($groups as &$group) {
            $group['url'] = LaravelLocalization::localizeURL('/dict/lemma/' . $group['lemma_id']);

            foreach ($group['items'] as &$item) {
                $query = array_merge($filters, [
                    'partner_meaning_id' => $item['meaning_id'],
                    'page' => 1,
                    'per_page' => 20,
                ]);
                unset($query['partner_pos_id']);

                $item['examples_url'] = $this->url($root . '/examples/search', $query);
                $item['editor_url'] = $this->url($root . '/collocations', array_merge(
                    $context,
                    ['partner_id' => $item['meaning_id']]
                ));
                $item['meaning_url'] = $group['url'] . '#meaning-' . $item['meaning_id'];
                $item['delete_url'] = $item['collocation_id'] ? LaravelLocalization::localizeURL($root . '/examples/candidates/' . $item['collocation_id']) : null;
            }
            unset($item);
        }
        unset($group);

        return $groups;
    }

    protected function url($path, array $query)
    {
        return LaravelLocalization::localizeURL($path) . '?' . http_build_query($query);
    }
}
