@extends('layouts.page')

@section('page_title', @trans('navigation.monuments'))

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/monument/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
            
        @if (User::checkAccess('corpus.edit'))
            | @include('widgets.form.button._edit', ['route' => '/corpus/monument/'.$monument->id.'/edit'])
            | @include('widgets.form.button._delete', ['route' => 'monument.destroy', 'args'=>['id' => $monument->id]]) 
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif 
            | <a href="">{{ trans('messages.history') }}</a>
        </p>
        
        @if ($monument->author)
        <h3>{{ $monument->author }}</h3>
        @endif
        <h2>{{ $monument->title }}</h2>
        
        @if ($monument->lang)
        <p><b>{{ trans('monument.lang') }}:</b> {{ $monument->lang->name }}@if ($monument->dialect), {{ $monument->dialect->name }}@endif</p>
        @endif
        
        @if ($monument->place)
        <p><b>{{ trans('monument.place') }}:</b> {{ $monument->place }}</p>
        @endif
        
        @if ($monument->publ_date)
        <p><b>{{ trans('monument.publ_date') }}:</b> {{ $monument->publ_date }}</p>
        @endif
        
        @if ($monument->pages)
        <p><b>{{ trans('monument.pages') }}:</b> {{ $monument->pages }}</p>
        @endif
        
        @if ($monument->bibl_descr)
        <p><b>{{ trans('monument.bibl_descr') }}:</b> {{ $monument->bibl_descr }}</p>
        @endif
        
        @if ($monument->graphic_id)
        <p><b>{{ trans('monument.graphic') }}:</b> {{ $monument->graphic_id && !empty(trans('monument.graphic_values')[$monument->graphic_id]) 
            ? trans('monument.graphic_values')[$monument->graphic_id] : null }}</p>
        @endif
        
        @if ($monument->has_trans !== null)
        <p><b>{{ trans('monument.has_trans') }}:</b> {{ $monument->has_trans && !empty(trans('monument.has_trans_values')[$monument->has_trans]) 
            ? trans('monument.has_trans_values')[$monument->has_trans] : null }}</p>
        @endif
        
        @if ($monument->volume)
        <p><b>{{ trans('monument.volume') }}:</b> {{ $monument->volume }}</p>
        @endif
        
        @if ($monument->type_id)
        <p><b>{{ trans('monument.type') }}:</b> {{ isset(trans('monument.type_values')[$monument->type_id]) 
            ? trans('monument.type_values')[$monument->type_id] : null }}</p>
        @endif
        
        @if ($monument->is_printed !== null)
        <p><b>{{ trans('monument.is_printed') }}:</b> {{ isset(trans('monument.is_printed_values')[$monument->is_printed]) 
            ? trans('monument.is_printed_values')[$monument->is_printed] : null }}</p>
        @endif
        
        @if ($monument->is_full !== null)
        <p><b>{{ trans('monument.is_full') }}:</b> {{ isset(trans('monument.is_full_values')[$monument->is_full]) 
            ? trans('monument.is_full_values')[$monument->is_full] : null }}</p>
        @endif
        
        @if ($monument->dcopy_link)
        <p><b>{{ trans('monument.dcopy_link') }}:</b> {!! text_to_html($monument->dcopy_link) !!}</p>
        @endif
        
        @if ($monument->publ)
        <p><b>{{ trans('monument.publ') }}:</b> {!! text_to_html($monument->publ) !!}</p>
        @endif
        
        @if ($monument->study)
        <p><b>{{ trans('monument.study') }}:</b> {!! text_to_html($monument->study) !!}</p>
        @endif
        
        @if ($monument->archive)
        <p><b>{{ trans('monument.archive') }}:</b> {{ $monument->archive }}</p>
        @endif
        
        @if ($monument->comment)
        <p><b>{{ trans('monument.comment') }}:</b> {{ $monument->comment }}</p>
        @endif
        
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/meaning.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
/*    loadPhoto('monument', {{$monument->id}}, '/dict/monument/{{$monument->id}}/photo_preview');*/
@stop

