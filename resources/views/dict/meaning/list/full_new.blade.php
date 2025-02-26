@extends('layouts.page')

@section('page_title')
{{ trans('navigation.new_meanings') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')
        @foreach ($new_meanings as $cr_date =>$meanings)
        <p class="date">{{$cr_date}}</p>
            @foreach ($meanings as $meaning)
            <div class="date-b">
                <div class="time">{{$meaning->created_at->formatLocalized("%H:%M")}}</div>
                <div class="event">
                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma')}}/{{$meaning->lemma_id}}">{{$meaning->lemma->lemma}}</a> 
                    @if (!empty($meaning->user))({{$meaning->user}}),@endif
                    @if (!empty($meaning->lemma) && !empty($meaning->lemma->lang)) <i>{{ $meaning->lemma->lang->code }}</i>,@endif 
                    @if (!empty($meaning->lemma) && !empty($meaning->lemma->pos))<b>{{$meaning->lemma->pos->code}}</b>,@endif
                    {{$meaning->getMultilangMeaningTextsString()}}
                </div>
            </div> 
            @endforeach
        @endforeach
@stop


