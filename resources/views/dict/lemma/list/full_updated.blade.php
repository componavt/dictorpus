@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_new_list/')}}">{{trans('dict.new_lemmas')}}</a></p>
        
        @foreach ($last_updated_lemmas as $cr_date =>$lemmas)
        <p class="date">{{$cr_date}}</p>
            @foreach ($lemmas as $lemma)
            <div class="date-b">
                <div class="time">{{$lemma->updated_at->formatLocalized("%H:%M")}}</div>
                <div class="event">
                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma')}}/{{$lemma->id}}">{{$lemma->lemma}}</a> 
                    (@if (isset($lemma->user)){{$lemma->user}}@endif)
                </div>
            </div> 
            @endforeach
        @endforeach
@stop


