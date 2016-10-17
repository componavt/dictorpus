
@extends('layouts.master')

@section('title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.texts') }}</h1>
        
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}">{{ trans('messages.back_to_list') }}</a>
            
        @if (User::checkAccess('corpus.edit'))
            | @include('widgets.form._button_edit', ['route' => '/corpus/text/'.$text->id.'/edit'])
            | @include('widgets.form._button_delete', ['route' => 'text.destroy', 'id' => $text->id]) 
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif 
            | <a href="/corpus/text/{{ $text->id }}/history">{{ trans('messages.history') }}</a>
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
        <?php $markup_text = $text->text_xml 
                    ? str_replace("<s id=\"","<s class=\"sentence\" id=\"text_s",$text->text_xml) 
                    : nl2br($text->text); ?>
                    <div id="text">{!! $markup_text !!}</div>
        @endif      
                </td>
                
        @if ($text->transtext)
                <td>
            @if ($text->transtext->title)
                    <h4>{{ $text->transtext->title }}<br>
                    ({{ $text->transtext->lang->name }})</h4>
            @endif      
            @if ($text->transtext->text)
            <?php $markup_text = $text->transtext->text_xml 
                            ? str_replace("<s id=\"","<s class=\"trans_sentence\" id=\"transtext_s",$text->transtext->text_xml) 
                            : nl2br($text->transtext->text); ?>
                    <div id="transtext">{!! $markup_text !!}</div>
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
    
    $(".sentence").hover(function(){
        var trans_id = 'trans' + $(this).attr('id');
        $(".trans_sentence").css('background','none');
        $("#"+trans_id).css('background','yellow');
    });
@stop

