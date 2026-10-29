<?php
$collocation_groups = collect($meaning->collocationsByCategory())->values();

$can_edit_collocations = User::checkAccess('dict.edit');

$preview_group_limit = 2;
$group_count = $collocation_groups->count();

$preview_groups = $collocation_groups
    ->take($preview_group_limit)
    ->all();

$remaining_groups = $collocation_groups
    ->slice($preview_group_limit)
    ->values()
    ->all();
?>

@if ($group_count || $can_edit_collocations)
    <div class="meaning-collocations">
        <div class="meaning-collocations-header">
            <strong>
                {{ mb_strtolower(trans('collocations.title')) }}
            </strong>

            @if ($can_edit_collocations)
                <a class="meaning-collocations-edit" href="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$meaning->id.'/collocations') }}" title="{{ trans('collocations.edit') }}" aria-label="{{ trans('collocations.edit') }}">
                    <i class="fa fa-pencil-alt fa-lg" aria-hidden="true"></i>
                </a>
            @endif
        </div>

        @include('dict.collocation._groups', ['collocation_groups' => $preview_groups])

        @if (count($remaining_groups))
            <details class="meaning-collocations-more">
                <summary>
                    <span class="meaning-collocations-expand-label">
                        {{ trans('collocations.show_all_categories', ['count' => $group_count]) }}
                    </span>

                    <span class="meaning-collocations-collapse-label">
                        {{ trans('collocations.collapse_categories') }}
                    </span>
                </summary>

                <div class="meaning-collocations-more-body">
                    @include('dict.collocation._groups', ['collocation_groups' => $remaining_groups])
                </div>
            </details>
        @endif
    </div>
@endif