@extends('layouts.master')

@section('title')
{{ trans('navigation.texts') }}
@stop

@section('content')
        <h2>{{trans('corpus.new_texts')}}</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_updated_list/')}}">{{trans('corpus.last_updated_texts')}}</a></p>
        @foreach ($new_texts as $cr_date =>$texts)
        <p class="date">{{$cr_date}}</p>
            @foreach ($texts as $text)
            <div class="date-b">
                <div class="time">{{$text->created_at->formatLocalized("%H:%M")}}</div>
                <div class="event">
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text')}}/{{$text->id}}">{{$text->title}}</a> 
                    ({{$text->user}})
                </div>
            </div> 
            @endforeach
        @endforeach
@stop


