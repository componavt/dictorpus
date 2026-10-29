@extends('layouts.page')

@section('page_title')
{{ trans('collocation_candidates.title') }}
@stop

@section('headExtra')
{!! css('lemma') !!}
{!! css('meaning-candidates') !!}
@stop

@section('body')
<div class="meaning-candidates">
    @include('dict.meaning.examples._header')

    <h2>{{ trans('collocation_candidates.title') }}</h2>
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