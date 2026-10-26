<div class="example-review-block @if ($fully_checked) example-review-checked @endif">
    <h3>{{ $word->word }}</h3>
        @if ($read_only && (User::checkAccess('dict.edit') || User::checkAccess('corpus.edit')))
        <p class="text-muted">
            {{ trans('meaning_examples.fragment_read_only') }}
        </p>
    @endif

    @foreach ($meaning_values as $meaning)
        <div class="lemma_b">
            <p>
                <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$meaning->lemma_id) }}">{{ $meaning->lemma->lemma }}</a><sup>{{ $meaning->meaning_n }}</sup>
                <span>{{ $meaning->lemma->pos->name }}</span>
            </p>

            <p class="meanings_b">
                {{ $meaning->getMultilangMeaningTextsString(LaravelLocalization::getCurrentLocale()) }}

                @if ($can_edit && !$meaning_checked)
                    <button
                        type="button"
                        class="example-review-plus"
                        data-meaning-id="{{ $meaning->id }}"
                        onclick="checkExampleMeaning(this)"
                        title="{{ trans('corpus.mark_right_meaning') }}"
                        aria-label="{{ trans('corpus.mark_right_meaning') }}"
                    >
                        <span class="fa fa-plus" aria-hidden="true"></span>
                    </button>
                @endif
            </p>
        </div>
    @endforeach

    @if ($add_collocation_url)
        <p class="example-add-collocation">
            <a href="{{ $add_collocation_url }}">
                {{ trans('meaning_examples.add_collocation') }}
            </a>
        </p>
    @endif

    @if (count($gramset_values))
        <div class="gramsets_b">
            @foreach ($gramset_values as $gramset)
                <p>
                    {{ $gramset->gramsetString() }}

                                        @if (!$gramset_checked && !empty($wordforms[$gramset->wordform_id]))
                        <span class="text-muted">
                            ({{ $wordforms[$gramset->wordform_id] }})
                        </span>
                    @endif

                    @if ($can_edit && !$gramset_checked)
                        <button
                            type="button"
                            class="example-review-plus"
                            data-gramset-id="{{ $gramset->id }}"
                            data-wordform-id="{{ $gramset->wordform_id }}"
                            onclick="checkExampleGramset(this)"
                            title="{{ trans('corpus.mark_right_gramset') }}"
                            aria-label="{{ trans('corpus.mark_right_gramset') }}"
                        >
                            <span class="fa fa-plus" aria-hidden="true"></span>
                        </button>
                    @endif
                </p>
            @endforeach
        </div>
    @endif

    @if ($can_reset)
        <div class="example-review-actions">
            <button
                type="button"
                class="example-review-reset"
                onclick="resetExampleAnnotation(this)"
                title="{{ trans('meaning_examples.reset') }}"
                aria-label="{{ trans('meaning_examples.reset') }}"
            >
                <span class="fa fa-sync-alt fa-lg" aria-hidden="true"></span>
            </button>
        </div>
    @endif    
</div>