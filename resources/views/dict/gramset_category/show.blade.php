@extends('layouts.page')

@section('page_title')
{{ trans('navigation.gramset_categories') }}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/gramset_category/') }}">{{ trans('messages.back_to_list') }}</a>
            
        @if (User::checkAccess('ref.edit'))
            | @include('widgets.form.button._edit', ['route' => '/dict/gram/'.$gram->id.'/edit'])
            | @include('widgets.form.button._delete', ['route' => 'gram.destroy', 'id' => $gram->id]) 
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif 
            | <a href="">{{ trans('messages.history') }}</a>
        </p>
        
        <p><i>{{ trans('dict.name')}} {{ trans('messages.in_english') }}:</i> <b>{{ $gramset_category->name_en}}</b></p>
        
        <p><i>{{ trans('dict.name')}} {{ trans('messages.in_russian') }}:</i> <b>{{ $gramset_category->name_ru}}</b></p>
        
        <p><i>{{ trans('messages.sequence_number') }}:</i> <b>{{ $gramset_category->sequence_number}}</b></p>
        
        
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/gramset_category');
@stop

