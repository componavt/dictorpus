<?php $column_title = 'name_'. LaravelLocalization::getCurrentLocale(); ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.parts_of_speech') }}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/pos/') }}">{{ trans('messages.back_to_list') }}</a>
            
        @if (User::checkAccess('ref.edit'))
            | @include('widgets.form.button._edit', ['route' => '/dict/pos/'.$pos->id.'/edit'])
        @else
            | {{ trans('messages.edit') }}
        @endif 
        </p>
        
        <h2>{{ $pos->{$column_title} }}</h2>
        
        <p><i>{{ trans('messages.category')}}:</i> <b>{{ trans('dict.pos_category_'.$pos->category) }}</b></p>
        
        <p><i>{{ trans('dict.name')}} {{ trans('messages.in_russian') }}:</i> <b>{{ $pos->name_ru}}</b></p>
        
        <p><i>{{ trans('dict.name_short')}} {{ trans('messages.in_russian') }}:</i> <b>{{ $pos->name_short_ru}}</b></p>
        
        <p><i>{{ trans('dict.name')}} {{ trans('messages.in_english') }}:</i> <b>{{ $pos->name_en}}</b></p>
        
        <p><i>{{ trans('messages.code')}}:</i> <b>{{ $pos->code }}</b></p>
        
        <p><i>{{ trans('dict.without_gram') }}:</i> <b>{{ trans('messages.bin_answer_'.($pos->without_gram ?? 0)) }}</b></p>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop

