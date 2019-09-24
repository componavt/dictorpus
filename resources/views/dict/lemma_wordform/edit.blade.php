@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('body')
    {!! Form::model($lemma, array('method'=>'PUT', 'route' => array('lemma_wordform.update', $lemma->id))) !!}
    @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
<input type="hidden" name="dialect_id_for_bases" value="{{$dialect_id}}">
    
<div class="row">
    <div class="col-sm-6">
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_lemma')}}: {{ $lemma->lemma}}</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a></p>
        
        <p><b>{{ trans('dict.lang') }}:</b> {{ $lemma->lang->name}}</p>
        <p><b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}</p>
        
    @if ($dialect_id)
        @include('widgets.form.button._red', [
            'id_name' => 'generate-wordforms',
            'on_click'=> 'copyBases('. $lemma->id. ')', 
            'title' => trans('dict.copy_bases')
            ])
        @include('widgets.form.button._red', [
            'on_click'=> 'fillWordforms('. $lemma->id. ', '. $dialect_id. ', '. sizeof($base_list).')', 
            'title' => trans('dict.generate_wordforms')
            ])
        @include('widgets.form.button._red', [
            'on_click'=> 'clearWordforms()', 
            'title' => trans('dict.clear_wordforms')
            ])
    @endif
        
    </div>
    
    @if ($dialect_id)
    <div class="col-sm-6">
    @include('dict.lemma_wordform._form_edit_bases')
    </div>
    @endif
</div>
    @include('dict.lemma_wordform._form_edit')

    @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
    {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/wordform.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop