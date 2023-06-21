@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_updated_list/')}}">{{trans('dict.last_updated_lemmas')}}</a></p>
        
        @foreach ($new_lemmas as $cr_date =>$lemmas)
        <p class="date">{{$cr_date}}</p>
            @foreach ($lemmas as $lemma)
            <div class="date-b">
                <div class="time">{{$lemma->created_at->formatLocalized("%H:%M")}}</div>
                <div class="event">
                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma')}}/{{$lemma->id}}">{{$lemma->lemma}}</a> 
                    @if (isset($lemma->user))({{$lemma->user}}),@endif
                    @if ($lemma->lang) <i>{{ $lemma->lang->code }}</i>,@endif 
                    @if ($lemma->pos)<b>{{$lemma->pos->code}}</b>,@endif
                    {{join('; ',$lemma->getMultilangMeaningTexts())}}
                </div>
            </div> 
            @endforeach
        @endforeach
@stop


