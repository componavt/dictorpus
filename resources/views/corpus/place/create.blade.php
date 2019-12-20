@extends('layouts.page')

@section('page_title')
{{ trans('navigation.places') }}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/place/') }}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('place.store'))) !!}
        @include('corpus.place._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create',
                                      'lang_id' => NULL,
                                      'dialect_value' => [],
                                      'region_values' => $region_values,
                                      'district_values' => $district_values])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop