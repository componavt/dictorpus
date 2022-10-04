<?php
    $styles = [
        '10'=>'relevance-10',
        '7'=>'relevance-7',
        '5'=>'relevance-5',
        '3'=>'relevance-3',
        ];
?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/text.css')!!}
    {!!Html::style('css/select2.min.css')!!}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_example')}}</h2>
        <div style="margin-bottom: 10px;">@include('dict.lemma.example.sentence', ['relevance'=>'', 'count'=>'', 'with_links' => false])</div>
        <div id="fragment">
            @include('dict.lemma.example._fragment', 
                ['sentence_obj'=>$sentence['sent_obj'], 'w_id'=>$sentence['w_id']])
        </div>
        @include('dict.lemma.example._translations')

        <p><a href="{{ LaravelLocalization::localizeURL($back_to_url) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a></p>
        

        {!! Form::open(array('method'=>'POST', 'route' => $route)) !!}
        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
        <input type="hidden" name="back_to_url" value="{{$back_to_url}}">
        
        <table class="table lemma-example-edit">
            @foreach ($meanings as $meaning)
            <tr>
                <td>
                    <h3>{{$meaning->lemma->lemma}} ({{$meaning->lemma->pos->name}})</h3>
                    {{$meaning->meaning_n}} {{trans('dict.meaning')}}
                    @if (isset($meaning_texts[$meaning->id]))
                        @foreach ($meaning_texts[$meaning->id] as $lang_name => $meaning_text)
                        <p><b>{{$lang_name}}:</b> {{$meaning_text}}</p>
                        @endforeach
                    @endif
                </td>
                <td class="lemma-example-edit-right">
                    @include('widgets.form.formitem._select',
                            ['name' => 'relevance['.$meaning->id.'_'.$sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id'].']',
                             'values' => trans('dict.relevance_scope'),
                             'value' => $meaning->relevance ?? 1,
                            ])
                </td>
            </tr>
            @endforeach
        </table>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
        {!! Form::close() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/lemma.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/meaning.js')!!}
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/text.js')!!}
    {!!Html::script('js/sentence.js')!!}
@stop

@section('jqueryFunc')
{{-- show/hide a block with meanings and gramsets --}}
    showLemmaLinked({{$sentence['text']->id}});    
{{-- show/hide a block with lemmas --}}
    showWordBlock('{{LaravelLocalization::getCurrentLocale()}}'); 
    
    addWordform('{{$sentence['text']->id}}','{{$sentence['text']->lang_id}}', '{{LaravelLocalization::getCurrentLocale()}}');
    posSelect(false);
{{--    checkLemmaForm(); --}}
    toggleSpecial();
    
    addTranslation({{ $sentence['sent_obj']->id }}, {{$sentence['w_id']}});
@stop

