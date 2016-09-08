
@extends('layouts.master')

@section('title')
{{ trans('navigation.texts') }}
@stop

@section('content')
        <h1>{{ trans('navigation.texts') }}</h1>
        
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}">{{ trans('messages.back_to_list') }}</a>
            
{{--        @if (User::checkAccess('corpus.edit'))
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'/edit') }}">{{ trans('messages.edit') }}</a> 
            | {!! $text->buttonDelete(false) !!}
        @else
            {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif --}}
            | <a href="">{{ trans('messages.history') }}</a>
        </p>
        
        <h2>{{ $text->title }}</h2>
        <h3>{{ trans('corpus.corpus') }}: {{ $text->corpus->name }}</h3>
        <p><i>{{ $labels }}</i></p>

        @if ($text->event)
        <p> 
            @include('corpus.event._to_string',['event'=>$text->event, 'lang_id' => $text->lang_id])
        </p>
        @endif
        
        @if ($text->source)
        <p> 
            @include('corpus.source._to_string',['source'=>$text->source])
        </p>
        @endif
        
        <table class="corpus-text">
            <tr valign='top'>
                <td>
        @if ($text->title)
                    <h4>{{ $text->title }}<br>
                    ({{ $text->lang->name }})</h4>
        @endif      
        
        @if ($text->text)
        <?php $text->text = str_replace("\n",'<br>',$text->text); ?>
                    <p>{!! $text->text !!}</p>
        @endif      
                </td>
                
        @if ($text->transtext)
                <td>
            @if ($text->transtext->title)
                    <h4>{{ $text->transtext->title }}<br>
                    ({{ $text->transtext->lang->name }})</h4>
            @endif      
            @if ($text->transtext->text)
            <?php $text->transtext->text = str_replace("\n",'<br>',$text->transtext->text); ?>
                    <p>{!! $text->transtext->text !!}</p>
            @endif      
        @endif      
            </tr>
        </table>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/text');
@stop

