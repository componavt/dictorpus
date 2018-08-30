@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/history.css')!!}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
        </p>

        <h2>{{ $text->title }}</h2>
        @include('widgets.history._history', ['all_history' => $text->allHistory()])
@stop        