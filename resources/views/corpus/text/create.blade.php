@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}">{{ trans('messages.back_to_list') }}</a>
           |
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
            | <a href="{{ LaravelLocalization::localizeURL('/help/text/form') }}">? {{ trans('navigation.help') }}</a>
        </p>

        @include('corpus.text.modals_for_edition')
        
        {!! Form::open(array('method'=>'POST', 'route' => array('text.store'))) !!}
        @include('corpus.text._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create',
                                      'recorder_value' => [], 
                                      'genre_value' => [], 
                                      'dialect_value'  => [] ])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/corpus.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    $(".multiple-select").select2();
    selectDialect('lang_id');
@stop