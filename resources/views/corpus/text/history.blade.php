@extends('layouts.master')

@section('title')
{{ trans('navigation.texts') }}
@stop

@section('content')
        <h1>{{ trans('navigation.texts') }}</h1>

        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}">{{ trans('messages.back_to_show') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}">{{ trans('messages.back_to_list') }}</a>
        </p>

        <h2>{{ $text->title }}</h2>
        <h3>{{ trans('messages.history') }}</h3>

        @foreach($text->revisionHistory as $history )
            <?php $user = \App\Models\User::find($history->userResponsible()->id);?>
            <li>
                <i>{{ $history->updated_at }}</i>
                {{ $user->name }} 
                {{trans('messages.changed')}} 
                {{ $history->fieldName() }} 
                {{trans('messages.from')}} 
                <b>{{ $history->oldValue() }}</b> 
                {{trans('messages.to')}} 
                <b>{{ $history->newValue() }}</b>
            </li>
        @endforeach
@stop        