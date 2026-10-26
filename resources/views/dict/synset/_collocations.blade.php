<div class="synset-collocations" id="synset-collocations" data-synset-id="{{ $synset->id }}" data-leave-confirm="{{ trans('collocations.synset_leave_confirm') }}">
    <h3>{{ trans('collocations.synset_title') }}</h3>

    <p class="help-block">
        {{ trans('collocations.synset_saved_members_hint') }}
    </p>

    <p class="help-block">
        {{ trans('collocations.synset_refresh_hint') }}
    </p>

    <div class="synset-collocations-view-switch">
        <button type="button" class="btn btn-default active synset-collocations-view" data-view="words" aria-controls="synset-collocations-words" aria-pressed="true">
            {{ trans('collocations.view_words') }}
        </button>

        <button type="button" class="btn btn-default synset-collocations-view" data-view="matrix" aria-controls="synset-collocations-matrix" aria-pressed="false">
            {{ trans('collocations.view_matrix') }}
        </button>
    </div>

    <div id="synset-collocations-words">
        @foreach (['dict.core' => $core_members, 'dict.periphery' => $periphery_members] as $section_key => $members)
        @if (!$members->isEmpty())
        <h4>{{ trans($section_key) }}</h4>

        @foreach ($members as $member)
        <?php
        $collocation_groups = $member->collocationsByCategory();
        $member_url = LaravelLocalization::localizeURL('/dict/lemma/' . $member->lemma_id) . '#meaning-' . $member->id;
        $editor_url = LaravelLocalization::localizeURL('/dict/meaning/' . $member->id . '/collocations') . '?' . http_build_query(['synset_id' => $synset->id]);
        $examples_url = LaravelLocalization::localizeURL('/dict/meaning/' . $member->id . '/examples/search') . '?' . http_build_query(['synset_id' => $synset->id]);
        ?>

        <div class="synset-collocation-member">
            <div class="synset-collocation-header">
                <span class="synset-collocation-name">
                    <a href="{{ $member_url }}">{{ $member->lemma->lemma }}</a><sup>{{ $member->meaning_n }}</sup>
                </span>

                @if ((int) $synset->dominant_id === (int) $member->id)
                <small class="text-muted">{{ trans('dict.dominant') }}</small>
                @endif

                @if (User::checkAccess('dict.edit'))
                <a href="{{ $editor_url }}" title="{{ trans('collocations.synset_edit') }}" aria-label="{{ trans('collocations.synset_edit') }}">
                    <i class="fa fa-pencil-alt fa-lg" aria-hidden="true"></i>
                </a>
                @endif
                <a href="{{ $examples_url }}">
                    {{ trans('meaning_examples.all_examples') }}
                </a>
            </div>

            @include('dict.collocation._groups', ['collocation_groups' => $collocation_groups])
        </div>
        @endforeach
        @endif
        @endforeach
    </div>

    <div id="synset-collocations-matrix" hidden>
        @include('dict.synset._collocations_matrix')
    </div>
</div>