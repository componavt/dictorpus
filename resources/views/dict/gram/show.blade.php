@extends('layouts.page')

@section('page_title')
{{ trans('navigation.grams') }}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/gram/') }}">{{ trans('messages.back_to_list') }}</a>
            
        @if (User::checkAccess('ref.edit'))
            | @include('widgets.form.button._edit', ['route' => '/dict/gram/'.$gram->id.'/edit'])
            | @include('widgets.form.button._delete', ['route' => 'gram.destroy', 'args'=>['id' => $gram->id]]) 
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif 
            | <a href="">{{ trans('messages.history') }}</a>
        </p>
        
        <h2>{{ $gram->name }}</h2>
        
        <p><i>{{ trans('dict.name_short')}} {{ trans('messages.in_english') }}:</i> <b>{{ $gram->name_short_en}}</b></p>
        
        <p><i>{{ trans('dict.name')}} {{ trans('messages.in_english') }}:</i> <b>{{ $gram->name_en}}</b></p>
        
        <p><i>{{ trans('dict.name_short')}} {{ trans('messages.in_russian') }}:</i> <b>{{ $gram->name_short_ru}}</b></p>
        
        <p><i>{{ trans('dict.name')}} {{ trans('messages.in_russian') }}:</i> <b>{{ $gram->name_ru}}</b></p>
        
        <p><i>{{ trans('dict.conll')}}:</i> <b>{{ $gram->conll }}</b></p>
        
        <p><i>{{ trans('dict.unimorph')}}:</i> <b>{{ $gram->unimorph }}</b></p>
        
        <p><i>{{ trans('messages.sequence_number') }}:</i> <b>{{ $gram->sequence_number}}</b></p>
        
        
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop

