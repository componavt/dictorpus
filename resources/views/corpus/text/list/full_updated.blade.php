@extends('layouts.page')

@section('page_title')
{{ trans('corpus.last_updated_texts') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_new_list/')}}">{{trans('corpus.new_texts')}}</a></p>
        @foreach ($last_updated_texts as $cr_date =>$texts)
        <p class="date">{{$cr_date}}</p>
            @foreach ($texts as $text)
            <div class="date-b">
                <div class="time">{{$text->updated_at->formatLocalized("%H:%M")}}</div>
                <div class="event">
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text')}}/{{$text->id}}">{{$text->title}}</a> 
                    (@if (isset($text->user)){{$text->user}}@endif), <i>{{$text->lang->name}}</i>
                </div>
            </div> 
            @endforeach
        @endforeach
@stop


