@extends('layouts.page')

@section('page_title')
{{ trans('collocation_candidates.title') }}
@stop

@section('headExtra')
{!! css('select2.min') !!}
{!! css('lemma') !!}
{!! css('meaning-candidates') !!}
{!! css('meaning-example-form') !!}
@stop

@section('body')
<div class="meaning-candidates">
    @include('dict.meaning.examples._header')

    <p class="help-block">{{ trans('collocation_candidates.hint') }}</p>

    @include('dict.meaning.candidates._filters')

    <hr>

    @if (count($groups))
        <p class="help-block">{{ trans('collocation_candidates.counts_hint') }}</p>
        <p class="help-block">{{ trans('collocation_candidates.counts_overlap') }}</p>
        @include('dict.meaning.candidates._table')
    @else
        <p>{{ trans('collocation_candidates.empty') }}</p>
    @endif

    @include('dict.meaning.candidates._pagination')
</div>
@stop

@section('footScriptExtra')
{!! js('select2.min') !!}
{!! js('meaning_example_filters') !!}
@stop

@section('jqueryFunc')
    initMeaningExampleFilters();
@stop