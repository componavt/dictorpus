<?php $column_title = 'name_'. LaravelLocalization::getCurrentLocale(); ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.gramsets') }}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/gramset/') }}">{{ trans('messages.back_to_list') }}</a>
            {{ \Request::route()->getName() }}
            
        @if (User::checkAccess('ref.edit'))
            | @include('widgets.form.button._edit', ['route' => '/dict/gramset/'.$gramset->id.'/edit'])
            | @include('widgets.form.button._delete', ['route' => 'gramset.destroy', 'args'=>['id' => $gramset->id]]) 
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif 
            | <a href="">{{ trans('messages.history') }}</a>
        </p>
        
        @foreach ($gram_fields as $field)
        <?php $column = 'gram'.ucfirst($field); ?>
        <p><i>{{ trans('dict.'.$field) }}:</i> <b>{{ $gramset->{$column}->getNameWithShort() }}</b></p>
        
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop

