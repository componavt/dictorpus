@extends('layouts.page')

@section('page_title')
{{ trans('navigation.places') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/place/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('place.store'))) !!}
        @include('corpus.place._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create',
                                      'dialect_value' => [],
                                      'region_values' => $region_values,
                                      'district_values' => $district_values])
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