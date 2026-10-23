<?php
$collocation_groups = $meaning->collocationsByCategory();
$can_edit_collocations = User::checkAccess('dict.edit');
?>

@if (count($collocation_groups) || $can_edit_collocations)
    <div class="meaning-collocations">
        <div class="meaning-collocations-header">
            <strong>{{ trans('collocations.title') }}</strong>

            @if ($can_edit_collocations)
                <a href="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$meaning->id.'/collocations') }}"
                   class="meaning-collocations-edit"
                   title="{{ trans('collocations.edit') }}"
                   aria-label="{{ trans('collocations.edit') }}">
                    <i class="fa fa-pencil-alt fa-lg" aria-hidden="true"></i>
                </a>
            @endif
        </div>

        @if (count($collocation_groups))
            <div class="meaning-collocations-groups">
                @foreach ($collocation_groups as $group)
                    <div class="meaning-collocations-group">
                        <p class="meaning-collocations-category">
                            <em>{{ $group['category']
                                ? $group['category']->name
                                : trans('collocations.without_category') }}:</em>
                        </p>

                        <p class="meaning-collocations-items">
                            @foreach ($group['collocations'] as $item_index => $collocation)
                                <?php
                                $partner_meaning = $collocation->collocateMeaning;

                                $partner_url = LaravelLocalization::localizeURL(
                                    '/dict/lemma/'.$partner_meaning->lemma_id
                                ).'#meaning-'.$partner_meaning->id;
                                ?>

                                <span class="collocation-item">
                                    <a href="{{ $partner_url }}"
                                       title="{{ trans('collocations.meaning', [
                                           'num' => $partner_meaning->meaning_n,
                                       ]) }}">{{ $partner_meaning->lemma->lemma }}</a><sup>{{ $partner_meaning->meaning_n }}</sup>@if ($collocation->display_gloss !== '') ‘{{ $collocation->display_gloss }}’@endif</span>@if ($item_index < count($group['collocations']) - 1),@else.@endif
                            @endforeach
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif