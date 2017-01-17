@extends('layouts.master')

@section('title')
{{ trans('navigation.gramsets') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.gramsets') }}</h1>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gramset/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('gramset.store'))) !!}
        @include('dict.gramset._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                                    'action' => 'create',
                                                    'pos_value' => [$url_args['pos_id']],
                                                    'lang_value' => [$url_args['lang_id']]
                                                   ])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
@stop

@section('jqueryFunc')
    $(".multiple-select").select2();
@stop
