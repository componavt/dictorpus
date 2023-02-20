@extends('layouts.page')

@section('page_title')
{{ trans('dict.dict_sound') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/mic.css')!!}
@stop

@section('body')        
    @include('corpus.informant._about')
    <p><a href="{{ LaravelLocalization::localizeURL('corpus/informant/'.$informant->id).'/audio'}}">Вернуться к информанту</a>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) .'/voiced' }}">{{trans('dict.check_voiced_lemmas')}}</a> ({{$informant->audios()->count()}})</p>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) }}">{{trans('dict.edit_list_for_voicing')}}</a> ({{$informant->lemmas()->count()}})</p>
    <p><a href="{{ LaravelLocalization::localizeURL('dict/audio/list/'.$informant->id) .'/choose'}}">{{trans('dict.add-lemmas-for-voicing')}}</a> ({{format_number($informant->unvoicedLemmasCount())}})</p>

    @include('dict.audio.list._record_block')
    <input type="hidden" id="informant_id" value="{{$informant->id}}">
@stop

@section('footScriptExtra')
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    @include('dict.audio._record_js', ['lemmas'=>$informant->lemmas])
@stop

