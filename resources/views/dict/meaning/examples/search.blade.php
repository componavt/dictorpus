@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
{!! css('select2.min') !!}
{!! css('lemma') !!}
{!! css('text') !!}
{!! css('meaning-examples') !!}
@stop

@section('body')
<div
    class="meaning-example-search" data-synset-id="{{ $synset_id }}"
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

    <form method="GET" action="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/search') }}">
        @if ($synset_id !== null)
        <input type="hidden" name="synset_id" value="{{ $synset_id }}">
        @endif

        <div class="form-group">
            <label for="example-search-partner">
                {{ trans('meaning_examples.partner') }}
            </label>

            <select
                id="example-search-partner"
                name="partner_meaning_id"
                class="form-control"
                data-search-url="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/partners') }}"
                data-placeholder="{{ trans('meaning_examples.partner_placeholder') }}"
                style="width: 100%;">
                <option value=""></option>

                @if ($partner)
                <option value="{{ $partner->id }}" selected>
                    {{ $partner_text }}
                </option>
                @endif
            </select>

            <p class="help-block">
                {{ trans('meaning_examples.partner_hint') }}
            </p>
        </div>

        <input type="hidden" name="only_checked" value="0">

        <div class="checkbox">
            <label>
                <input
                    type="checkbox"
                    name="only_checked"
                    value="1"
                    @if ($only_checked) checked @endif>
                {{ trans('meaning_examples.only_checked') }}
            </label>
        </div>

        <div id="example-search-partner-options">
            <input type="hidden" name="same_gramset" value="0">

            <div class="checkbox">
                <label>
                    <input type="checkbox" name="same_gramset" value="1" @if ($same_gramset) checked @endif>
                    {{ trans('meaning_examples.same_gramset') }}
                </label>
            </div>
        </div>

        <input type="hidden" name="whole_sentence" value="0">

        <div class="checkbox">
            <label>
                <input type="checkbox" name="whole_sentence" value="1" @if ($whole_sentence) checked @endif>
                {{ trans('meaning_examples.whole_sentence') }}
            </label>
        </div>

        <p class="help-block">
            {{ trans('meaning_examples.whole_sentence_hint') }}
        </p>

        <div class="example-distance-range">
            <label for="example-search-distance-from">
                {{ trans('meaning_examples.distance_from') }}
            </label>

            <input
                type="number"
                step="1"
                id="example-search-distance-from"
                name="distance_from"
                value="{{ $distance_from }}"
                class="form-control"
                required>

            <label for="example-search-distance-to">
                {{ trans('meaning_examples.distance_to_short') }}
            </label>

            <input
                type="number"
                step="1"
                id="example-search-distance-to"
                name="distance_to"
                value="{{ $distance_to }}"
                class="form-control"
                required>
        </div>

        <p class="help-block">
            {{ trans('meaning_examples.distance_hint') }}
        </p>

        <div class="form-group">
            <label for="example-search-per-page">
                {{ trans('meaning_examples.per_page') }}
            </label>

            <select
                id="example-search-per-page"
                name="per_page"
                class="form-control"
                style="width: auto;">
                @foreach (array_unique([2, 5, 10, 20, 50, $per_page]) as $value)
                <option
                    value="{{ $value }}"
                    @if ($per_page==$value) selected @endif>
                    {{ $value }}
                </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            {{ trans('meaning_examples.submit') }}
        </button>
    </form>

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
{!! js('text') !!}
{!! js('meaning_examples') !!}
@stop

@section('jqueryFunc')
    initExamplePartnerSelect();
    initExampleCollocationLinks();
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