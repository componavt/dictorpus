@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
{!! css('select2.min') !!}
{!! css('lemma') !!}
{!! css('text') !!}
{!! css('meaning-examples') !!}
{!! css('meaning-example-form') !!}
@stop

@section('body')
<div class="meaning-example-search" data-synset-id="{{ $synset_id }}"
    data-delete-collocation-confirm="{{ trans('meaning_examples.delete_collocation_confirm') }}"
    data-delete-collocation-error="{{ trans('meaning_examples.delete_collocation_error') }}"
    data-attach-error="{{ trans('collocation_examples.attach_error') }}"
    data-whole-sentence="{{ $whole_sentence ? '1' : '0' }}"
    data-collocation-recorded-label="{{ trans('meaning_examples.collocation_recorded') }}"
    data-distance-from="{{ $distance_from }}" data-examples-return="{{ $examples_return }}"
    data-distance-to="{{ $distance_to }}"
    data-word-block-url="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/word') }}"
    data-csrf-token="{{ csrf_token() }}"
    data-save-error="{{ trans('meaning_examples.save_error') }}"
    data-recorded-partners-url="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/recorded-partners') }}"
    data-add-collocation-label="{{ trans('meaning_examples.add_collocation') }}"
    data-delete-collocation-label="{{ trans('meaning_examples.delete_collocation') }}"
    data-delete-collocation-hint="{{ trans('meaning_examples.delete_collocation_hint') }}">
    @include('dict.meaning.examples._header')

    @if ($search_errors->count())
    <div class="alert alert-danger">
        <ul>
            @foreach ($search_errors->all() as $message)
            <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @include('dict.meaning.examples.form._form', [
        'candidate_mode' => false,
        'form_url' => LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/search'),
        'form_values' => array_merge($selected_filters, [
            'distance_from' => $distance_from,
            'distance_to' => $distance_to,
            'source_only_checked' => $source_only_checked,
            'only_checked' => $only_checked,
            'same_gramset' => $same_gramset,
            'whole_sentence' => $whole_sentence,
            'per_page' => $per_page,
        ]),
    ])

    @if ($sentences)
    <hr>

    <p>
        {{ trans('meaning_examples.total') }}:
        <span class="example-result-total">{{ $sentences->total() }}</span>
    </p>

    @if (count($sentences))
    <p class="example-rating-legend">
        @foreach (trans('dict.relevance_scope_example') as $rating_value => $rating_text)
        <span class="relevance relevance-{{ $rating_value }}">
            <span class="glyphicon glyphicon-star"></span>
            {{ $rating_text }}
        </span>
        @endforeach
    </p>
    
    @if ($whole_sentence)
        <p class="example-window-legend">
            {{ trans('meaning_examples.whole_sentence_results') }}
        </p>
    @else
        <p class="example-window-legend">
            <span class="example-window-word">
                {{ trans('meaning_examples.window_label', [
                    'from' => $distance_from,
                    'to' => $distance_to,
                ]) }}
            </span>
            —
            {{ trans('meaning_examples.window_hint') }}
        </p>
    @endif

    <ol start="{{ $sentences->firstItem() }}">
        @foreach ($sentences as $item)
        <li class="example-sentence-group">
            @foreach ($item['source_examples'] as $example)
            @include('dict.meaning.examples._card')
            @endforeach
        </li>
        @endforeach
    </ol>
    @elseif ($sentences->total() === 0)
    <p>{{ trans('meaning_examples.no_results') }}</p>
    @else
    <p>{{ trans('meaning_examples.empty_page') }}</p>
    @endif

    {!! $sentences->render() !!}
    @endif
</div>
@stop
@section('footScriptExtra')
{!! js('select2.min') !!}
{!! js('meaning_example_filters') !!}
{!! js('text') !!}
{!! js('meaning_examples') !!}
{!! js('collocation_examples') !!}
@stop

@section('jqueryFunc')
    initMeaningExampleFilters();
    initExamplePartnerSelect();
    initExampleCollocationLinks();
    initCollocationExampleAttach();
    updateExampleCollocationMarks();

    var example_page = $('.meaning-example-search');

    showWordBlock('{{ LaravelLocalization::getCurrentLocale() }}',
        example_page.attr('data-word-block-url'),
        {
            distance_from: example_page.attr('data-distance-from'),
            distance_to: example_page.attr('data-distance-to'),
            synset_id: example_page.attr('data-synset-id') || '',
            examples_return: example_page.attr('data-examples-return') || '',
            whole_sentence: example_page.attr('data-whole-sentence') || 0
        }
    );

    $('.meaning-example-sentence').on('click', '.links-to-lemmas',
        function (event) {
            event.stopPropagation();
        }
    );

    restoreExampleOriginal();
@stop