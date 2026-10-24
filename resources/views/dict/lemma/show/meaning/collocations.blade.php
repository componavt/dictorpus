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

        @include('dict.collocation._groups', [
            'collocation_groups' => $collocation_groups,
        ])
    </div>
@endif