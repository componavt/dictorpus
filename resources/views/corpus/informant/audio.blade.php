@extends('layouts.page')

@section('page_title')
{{ trans('navigation.informants') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
    @include('corpus.informant._about')
    
    <h3>{{trans('dict.dict_sound')}}</h3>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id. '/record') }}">{{trans('dict.voice_new_lemmas')}}</a> ({{$informant->lemmas()->count()}})</p>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) .'/voiced' }}">{{trans('dict.voiced_lemmas')}}</a> ({{$informant->audios()->count()}})</p>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) }}">{{trans('dict.list_for_voicing')}}</a> ({{$informant->lemmas()->count()}})</p>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) .'/choose'}}">{{trans('dict.add-lemmas-for-voicing')}}</a> 
        ({{ format_number($informant->unvoicedLemmasCount()) }} / {{ format_number($informant->unvoicedDialectLemmasCount()) }})</p>
@stop



