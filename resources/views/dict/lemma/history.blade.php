{{--
    http://localhost/ru/dict/lemma/3598/history
    http://localhost/ru/dict/lemma/56/history
    http://localhost/ru/dict/lemma/1386/history
    --}}
@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('content')
        <h1>{{ trans('navigation.lemmas') }}</h1>

        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}">{{ trans('messages.back_to_show') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}">{{ trans('messages.back_to_list') }}</a>
        </p>

        <h2>{{ $lemma->lemma }}</h2>
        <h3>{{ trans('messages.history') }}</h3>
        @foreach($lemma->allHistory() as $time => $histories )
        <?php $user = \App\Models\User::find($histories[0]->userResponsible()->id); ?>
        <p>
            <i>{{ $time }}</i>
            {{ $user->name }} 
            <ul>
            @foreach($histories as $history)
                @include('widgets.history.one_string')
            @endforeach
            </ul>
        </p>
        @endforeach
@stop        