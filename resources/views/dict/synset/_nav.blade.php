<?php
$synset = $synset ?? \App\Models\Dict\Synset::with('pos')->find($synset_id);
$args_by_get = $args_by_get ?? '';
$base = '/dict/synset/' . $synset->id;
$tabs = [
    'edit' => [$base . '/edit', 'dict.synset_tab_edit'],
    'collocations' => [$base . '/edit#synset-collocations', 'dict.synset_tab_collocations'],
    'examples' => [$base . '/examples/candidates', 'dict.synset_tab_examples'],
];
?>
<div class="synset-space">
    <p>
        <a href="{{ LaravelLocalization::localizeURL('/service/dict/synsets/') . $args_by_get }}">{{ trans('dict.synset_dictionary') }}</a>
        › <strong>{{ $synset->name }}</strong>
        @if ($synset->descr) — {{ $synset->descr }} @endif
    </p>
    <ul class="nav nav-tabs">
        @foreach ($tabs as $key => $tab)
        <li class="{{ ($active ?? '') === $key ? 'active' : '' }}">
            <a href="{{ LaravelLocalization::localizeURL(strtok($tab[0], '#')) . $args_by_get . (strpos($tab[0], '#') !== false ? '#' . substr(strstr($tab[0], '#'), 1) : '') }}">{{ trans($tab[1]) }}</a>
        </li>
        @endforeach
    </ul>
</div>