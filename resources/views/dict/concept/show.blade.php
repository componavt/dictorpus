@extends('layouts.page')

@section('page_title')
{{ trans('navigation.concepts') }}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/concept/') }}">{{ trans('messages.back_to_list') }}</a>
            
        @if (User::checkAccess('ref.edit'))
            | @include('widgets.form.button._edit', ['route' => '/dict/concept/'.$concept->id.'/edit'])
            | @include('widgets.form.button._delete', ['route' => 'concept.destroy', 'args'=>['id' => $concept->id]]) 
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif 
            | <a href="">{{ trans('messages.history') }}</a>
        </p>
        
<div class="row">
    <div class="col-sm-8">
        <h2>{{ $concept->text }}</h2>
        
        <p><b>{{ trans('dict.pos') }}:</b> {{ $concept->pos->name}}</p>
        <p><b>{{ trans('dict.name')}} {{ trans('messages.in_russian') }}:</b> {{ $concept->text_ru}}</p>        
        <p><b>{{ trans('dict.name')}} {{ trans('messages.in_english') }}:</b> {{ $concept->text_en}}</p>        
        <p><b>{{ trans('dict.descr')}} {{ trans('messages.in_russian') }}:</b> {{ $concept->descr_ru}}</p>        
        <p><b>{{ trans('dict.descr')}} {{ trans('messages.in_english') }}:</b> {{ $concept->descr_en}}</p>
                
    </div>
    <div class="col-sm-4 concept-page-photo">
        <div id='concept-photo_{{$concept->id}}'></div> 
    </div>
        
        
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/meaning.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    loadPhoto('concept', {{$concept->id}}, '/dict/concept/{{$concept->id}}/photo_preview');
@stop

