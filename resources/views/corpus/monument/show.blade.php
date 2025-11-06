@extends('layouts.page')

@section('page_title', @trans('navigation.monuments'))

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/monument/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
            
        @if (User::checkAccess('corpus.edit'))
            | @include('widgets.form.button._edit', ['route' => '/corpus/monument/'.$monument->id.'/edit'])
            | @include('widgets.form.button._delete', ['route' => 'monument.destroy', 'args'=>['id' => $monument->id]]) 
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif 
            | <a href="">{{ trans('messages.history') }}</a>
        </p>
        
        <h3>{{ $monument->author }}</h3>
        <h2>{{ $monument->title }}</h2>
        
        <p><b>{{ trans('monument.lang') }}:</b> {{ $monument->lang->name }}@if ($monument->dialect), {{ $monument->dialect->name }}@endif</p>
        <p><b>{{ trans('monument.place') }}:</b> {{ $monument->place }}</p>
        <p><b>{{ trans('monument.publ_date') }}:</b> {{ $monument->publ_date }}</p>
        <p><b>{{ trans('monument.pages') }}:</b> {{ $monument->pages }}</p>
        <p><b>{{ trans('monument.bibl_descr') }}:</b> {{ $monument->bibl_descr }}</p>
        <p><b>{{ trans('monument.graphic') }}:</b> {{ $monument->graphic_id && !empty(trans('monument.graphic_values')[$monument->graphic_id]) 
            ? trans('monument.graphic_values')[$monument->graphic_id] : null }}</p>
        <p><b>{{ trans('monument.has_trans') }}:</b> {{ $monument->has_trans && !empty(trans('monument.has_trans_values')[$monument->has_trans]) 
            ? trans('monument.has_trans_values')[$monument->has_trans] : null }}</p>
        <p><b>{{ trans('monument.volume') }}:</b> {{ $monument->volume }}</p>
        <p><b>{{ trans('monument.type') }}:</b> {{ isset(trans('monument.type_values')[$monument->type_id]) 
            ? trans('monument.type_values')[$monument->type_id] : null }}</p>
        <p><b>{{ trans('monument.is_printed') }}:</b> {{ isset(trans('monument.is_printed_values')[$monument->is_printed]) 
            ? trans('monument.is_printed_values')[$monument->is_printed] : null }}</p>
        <p><b>{{ trans('monument.is_full') }}:</b> {{ isset(trans('monument.is_full_values')[$monument->is_full]) 
            ? trans('monument.is_full_values')[$monument->is_full] : null }}</p>
        <p><b>{{ trans('monument.dcopy_link') }}:</b> {!! text_to_html($monument->dcopy_link) !!}</p>
        <p><b>{{ trans('monument.publ') }}:</b> {!! text_to_html($monument->publ) !!}</p>
        <p><b>{{ trans('monument.study') }}:</b> {!! text_to_html($monument->study) !!}</p>
        <p><b>{{ trans('monument.archive') }}:</b> {{ $monument->archive }}</p>
        <p><b>{{ trans('monument.comment') }}:</b> {{ $monument->comment }}</p>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/meaning.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
/*    loadPhoto('monument', {{$monument->id}}, '/dict/monument/{{$monument->id}}/photo_preview');*/
@stop

