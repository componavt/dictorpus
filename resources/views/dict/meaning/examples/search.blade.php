@extends('layouts.page')

@section('page_title')
    {{ trans('meaning_examples.title') }}
@stop

@section('headExtra')
    {!! css('select2.min') !!}
    {!! css('lemma') !!}
    {!! css('text') !!}
    {!! css('meaning-examples') !!}
@stop

@section('body')
    <div
        class="meaning-example-search"
        data-distance-from="{{ $distance_from }}"
        data-distance-to="{{ $distance_to }}"
        data-word-block-url="{{ LaravelLocalization::localizeURL('/dict/meaning/'.$source->id.'/examples/word') }}"
        data-csrf-token="{{ csrf_token() }}"
        data-save-error="{{ trans('meaning_examples.save_error') }}"
    >
        <p>
            {{ trans('meaning_examples.source') }}:
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$source->lemma_id) }}">
                {{ $source->lemma->lemma }}</a><sup>{{ $source->meaning_n }}</sup>            

            @if ($partner)
                —
                {{ trans('meaning_examples.partner') }}:
                <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$partner->lemma_id) }}">
                    {{ $partner->lemma->lemma }}</a><sup>{{ $partner->meaning_n }}</sup>                
            @endif
        </p>

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
                    style="width: 100%;"
                >
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
                        @if ($only_checked) checked @endif
                    >
                    {{ trans('meaning_examples.only_checked') }}
                </label>
            </div>

            <div
                id="example-search-partner-options"
                @if (!$partner) style="display: none;" @endif
            >
                <input type="hidden" name="same_gramset" value="0">

                <div class="checkbox">
                    <label>
                        <input
                            type="checkbox"
                            name="same_gramset"
                            value="1"
                            @if ($same_gramset) checked @endif
                        >
                        {{ trans('meaning_examples.same_gramset') }}
                    </label>
                </div>
            </div>

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
                    required
                >

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
                    required
                >
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
                    style="width: auto;"
                >
                    @foreach (array_unique([2, 5, 10, 20, 50, $per_page]) as $value)
                        <option
                            value="{{ $value }}"
                            @if ($per_page == $value) selected @endif
                        >
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
                {{ $sentences->total() }}
            </p>

            @if (count($sentences))
                <ol start="{{ $sentences->firstItem() }}">
                    @foreach ($sentences as $item)
                        <li style="margin-bottom: 15px;">
                            <div class="meaning-example-sentence">
                                {!! \App\Library\MeaningExampleFormatter::highlight($item) !!}
                            </div>

                            <div class="text-muted">
                                {{ trans('meaning_examples.source_relevance') }}:
                                {{ $item['source_relevance'] }};

                                {{ trans('meaning_examples.matches') }}:
                                {{ $item['match_count'] }}.

                                <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$item['text_id'].'?search_sentence='.$item['s_id']) }}">
                                    {{ trans('meaning_examples.text') }}
                                    № {{ $item['text_id'] }}
                                </a>
                            </div>
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

    var example_page = $('.meaning-example-search');

    showWordBlock('{{ LaravelLocalization::getCurrentLocale() }}',
        example_page.attr('data-word-block-url'),
        {
            distance_from: example_page.attr('data-distance-from'),
            distance_to: example_page.attr('data-distance-to')
        }
    );

    $('.meaning-example-sentence').on(
        'click',
        '.links-to-lemmas',
        function (event) {
            event.stopPropagation();
        }
    );

@stop