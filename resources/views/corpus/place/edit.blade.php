@extends('layouts.page')

@section('page_title')
{{ trans('navigation.places') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_place')}} <span class='imp'>"{{ $place->name}}"</span></h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/place/'.$place->id) }}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($place, array('method'=>'PUT', 'route' => array('place.update', $place->id))) !!}
        @include('corpus.place._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    selectDialect('lang_id', '{{trans('dict.select_dialect')}}');
@stop