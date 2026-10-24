<?php
$collocation_members = $synset->collocationMembersForList();
$synset_collocations_url = LaravelLocalization::localizeURL('/dict/synset/'.$synset->id.'/edit').$args_by_get.'#synset-collocations';
?>

@if ($collocation_members->isEmpty())
    <span class="text-muted">—</span>
@else
    @foreach ($collocation_members as $member)
        <details class="synset-list-collocation">
            <summary>
                <span class="synset-list-collocation-name">{{ $member->lemma->lemma }}<sup>{{ $member->meaning_n }}</sup></span>

                <small class="text-muted"
                       title="{{ trans('collocations.list_count', [
                           'count' => $member->collocations->count(),
                       ]) }}">
                    ({{ $member->collocations->count() }})
                </small>
            </summary>

            <div class="synset-list-collocation-body">
                @include('dict.collocation._groups', [
                    'collocation_groups' => $member->collocationsByCategory(),
                ])
            </div>
        </details>
    @endforeach
@endif

<div class="synset-list-collocations-link">
    <a href="{{ $synset_collocations_url }}">
        {{ trans('collocations.list_open_synset') }}
    </a>
</div>