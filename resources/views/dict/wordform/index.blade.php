<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('navigation.wordforms') }}
@stop

@section('content')
        <h2>{{ trans('navigation.wordforms') }}</h2>
        
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/wordform/with_multiple_lemmas') }}">{{ trans('dict.wordforms_linked_many_lemmas') }}</a></p>
        
        {!! Form::open(['url' => '/dict/wordform/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_text', 
                ['name' => 'search_wordform', 
                'value' => $url_args['search_wordform'],
                'attributes'=>['size' => 15,
                               'placeholder'=>trans('dict.wordform')]])
        @include('widgets.form._formitem_select', 
                ['name' => 'search_lang', 
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]]) 
        @include('widgets.form._formitem_select', 
                ['name' => 'search_pos', 
                 'values' =>$pos_values,
                 'value' =>$url_args['search_pos'],
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) {{ trans('messages.records') }}
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($wordforms)
        <br>
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.wordform') }}</th>
                <th>{{ trans('dict.gram_attr') }}</th>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.pos') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wordforms as $wordform)
                @foreach($wordform['lemmas'] as $key=>$lemma) 
            <tr>
                    @if ($key==0)
                <td rowspan='{{sizeof($wordform['lemmas'])}}'>{{ $list_count++ }}</td>
                <td rowspan='{{sizeof($wordform['lemmas'])}}'>{{$wordform->wordform}}</td>
                    @endif
                <td>
                    @if ($wordform->lemmaDialectGramset($lemma->id))
                    {{ $wordform->lemmaDialectGramset($lemma->id)->gramsetString()}}
                    @endif
                </td>
                <td>
                    @if (sizeof($wordform['lemmas'])>1)
                        {{$key+1}}.
                    @endif
                    <a href="lemma/{{$lemma->id}}{{$args_by_get}}">{{$lemma->lemma}}</a>
                </td>
                <td>
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                <td>
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                    @endif
                </td>
            </tr>
                @endforeach
            @endforeach
        </tbody>
        </table>
        @endif
        {!! $wordforms->appends($url_args)->render() !!}
@stop


