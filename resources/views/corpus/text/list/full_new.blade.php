@extends('layouts.page')

@section('page-title')
{{ trans('corpus.new_texts') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_updated_list/')}}">{{trans('corpus.last_updated_texts')}}</a></p>
        @foreach ($new_texts as $cr_date =>$texts)
        <p class="date">{{$cr_date}}</p>
            @foreach ($texts as $text)
            <div class="date-b">
                <div class="time">{{$text->created_at->formatLocalized("%H:%M")}}</div>
                <div class="event">
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text')}}/{{$text->id}}">{{$text->title}}</a> 
                    (@if (isset($text->user)){{$text->user}}@endif)
                </div>
            </div> 
            @endforeach
        @endforeach
@stop


