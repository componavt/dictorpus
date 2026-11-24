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
<div class="synset-space" style="margin-bottom: 20px">
    <div style="display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; column-gap: 25px;">
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/service/dict/synsets/') . $args_by_get }}">{{ trans('navigation.synsets') }}</a>
            › <strong>{{ $synset->name }}</strong>
            @if ($synset->descr) — {{ $synset->descr }} @endif
        </p>

        @if (!empty($vepkar_lemma))
        <p>
            {{ trans('navigation.lemmas') }}:
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$vepkar_lemma->id).(!empty($vepkar_meaning_id) ? '#meaning-'.$vepkar_meaning_id : '') }}">{{ $vepkar_lemma->lemma }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$vepkar_lemma->id.'/history') }}">{{ trans('messages.history') }}</a>
        </p>
        @endif
    </div>
    <ul class="nav nav-tabs">
        @foreach ($tabs as $key => $tab)
        <li class="{{ ($active ?? '') === $key ? 'active' : '' }}">
            <a href="{{ LaravelLocalization::localizeURL(strtok($tab[0], '#')) . $args_by_get . (strpos($tab[0], '#') !== false ? '#' . substr(strstr($tab[0], '#'), 1) : '') }}">{{ trans($tab[1]) }}</a>
        </li>
        @endforeach
    </ul>
</div>