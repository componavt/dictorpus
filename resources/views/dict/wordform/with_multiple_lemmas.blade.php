<?php $list_count = 1;?>

@extends('layouts.master')

@section('title')
{{ trans('navigation.wordforms') }}
@stop

@section('content')
        <h1>{{ trans('navigation.wordforms') }}</h1>
        <h2>{{ trans('dict.wordforms_linked_many_lemmas') }}</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/wordform/') }}">{{ trans('messages.back_to_list') }}</a></p>
      
        {!! Form::open(['url' => '/dict/wordform/with_multiple_lemmas', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_text', 
                ['name' => 'search_wordform', 
                 'special_symbol' => true,
                'value' => $url_args['search_wordform'],
                'attributes'=>['placeholder'=>trans('dict.wordform')]])
        @include('widgets.form._formitem_select', 
                ['name' => 'search_lang', 
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]]) 
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        {!! Form::close() !!}
        
        <p>{{ trans('messages.founded_records', ['count'=>count($wordforms)]) }}</p>

        @if ($wordforms)
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
                    <?php $gramsets = $wordform->lemmaDialectGramset($lemma->id); ?>
                    @if ($gramsets->count())
                        @foreach ($gramsets->get() as $gramset)
                        {{ $gramset->gramsetString()}}<br>
                        @endforeach
                    @endif
                </td>
                <td>
                    {{$key+1}}. <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}{{$args_by_get}}">{{$lemma->lemma}}</a>
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
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop

