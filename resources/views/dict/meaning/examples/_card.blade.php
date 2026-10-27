<div
    id="sentence-{{ $example['example_id'] }}"
    class="example-card"
    data-source-word-key="{{ $example['text_id'].'_'.$example['source_w_id'] }}"
    data-source-w-id="{{ $example['source_w_id'] }}"
    data-fragment-id="{{ (int) $example['fragment_id'] }}">
    @if ($example['is_fragment'] && (User::checkAccess('dict.edit') || User::checkAccess('corpus.edit')))
        <div class="text-muted example-fragment-label">
            {{ trans('meaning_examples.fragment') }}
        </div>
    @endif
    <div class="example-card-text">
        <span class="example-source-rating">
            @if ($example['source_relevance'] > 1)
            <span class="glyphicon glyphicon-star relevance-{{ $example['source_relevance'] }}"></span>
            @endif
        </span>

        <span class="meaning-example-sentence">
            {!! \App\Library\MeaningExampleFormatter::highlight($example, $word_blocks[$example['example_id']] ?? []) !!}
        </span>
    </div>

    @if ($example['translation_html'] !== '')
    <div class="example-card-translation">
        <i>{!! $example['translation_html'] !!}</i>
    </div>
    @endif

        @if ($example['is_fragment'] && (User::checkAccess('dict.edit') || User::checkAccess('corpus.edit')))
        <div class="example-original-panel">
            <button
                type="button"
                class="btn btn-link btn-sm example-original-toggle"
                data-url="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/original/'.$example['text_id'].'_'.$example['source_w_id']) }}"
                data-show="{{ trans('meaning_examples.show_original') }}"
                data-hide="{{ trans('meaning_examples.hide_original') }}"
                aria-expanded="false"
                onclick="toggleExampleOriginal(this)"
            >
                {{ trans('meaning_examples.show_original') }}
            </button>

            <div
                class="example-original-content"
                style="display: none;"
            ></div>
        </div>
    @endif

    <div class="example-card-footer">
        (<a href="{{ $example['text_url'] }}">{{ $example['text_title'] }}</a>)

        @if (User::checkAccess('dict.edit'))
        @include('widgets.form.button._edit', [
        'route' => '/dict/lemma/'.$source->lemma_id
        .'/edit/example/'.$example['text_id']
        .'_'.$example['s_id']
        .'_'.$example['source_w_id'],
        'args_by_get' => '?'.http_build_query([
        'examples_return' => $examples_return
        .'#sentence-'.$example['example_id'],
        ]),
        'link_class' => 'sentence-edit',
        'without_text' => 1,
        ])

        <span class="example-rating-actions">
            @foreach ([10, 7, 5] as $rating_value)
            <button
                type="button"
                class="example-rating-button relevance-{{ $rating_value }}"
                data-relevance="{{ $rating_value }}"
                onclick="rateSourceExample(this)"
                title="{{ trans('dict.add-example-'.$rating_value) }}"
                aria-label="{{ trans('dict.add-example-'.$rating_value) }}"
                @if ($example['source_relevance']==$rating_value) style="display: none;" @endif>
                <span class="fa fa-plus" aria-hidden="true"></span>
            </button>
            @endforeach

            <button
                type="button"
                class="example-rating-button"
                data-relevance="0"
                onclick="rateSourceExample(this)"
                title="{{ trans('dict.remove-example') }}"
                aria-label="{{ trans('dict.remove-example') }}">
                <span class="fa fa-times" aria-hidden="true"></span>
            </button>
        </span>
        @endif
    </div>
</div>