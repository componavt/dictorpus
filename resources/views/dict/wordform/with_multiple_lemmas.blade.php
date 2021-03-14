<?php $list_count = 1;?>

@extends('layouts.page')

@section('page_title')
{{ trans('navigation.wordforms') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <h2>{{ trans('dict.wordforms_linked_many_lemmas') }}</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/wordform/') }}">{{ trans('messages.back_to_list') }}</a></p>
      
        {!! Form::open(['url' => '/dict/wordform/with_multiple_lemmas', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form.formitem._text', 
                ['name' => 'search_wordform', 
                 'special_symbol' => true,
                'value' => $url_args['search_wordform'],
                'attributes'=>['placeholder'=>trans('dict.wordform')]])
        @include('widgets.form.formitem._select', 
                ['name' => 'search_lang', 
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]]) 
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
        {!! Form::close() !!}
        
        @include('widgets.founded_records', ['numAll'=>count($wordforms)])

        @if ($wordforms)
        <table class="table-bordered table-wide table-striped rwd-table wide-lg">
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
                <td data-th="No" rowspan='{{sizeof($wordform['lemmas'])}}'>{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.wordform') }}" rowspan='{{sizeof($wordform['lemmas'])}}'>{{$wordform->wordform}}</td>
                    @endif
                <td data-th="{{ trans('dict.gram_attr') }}">
                    <?php $gramsets = $wordform->lemmaDialectGramset($lemma->id); ?>
                    @if ($gramsets->count())
                        @foreach ($gramsets->get() as $gramset)
                        {{ $gramset->gramsetString()}}<br>
                        @endforeach
                    @endif
                </td>
                <td data-th="{{ trans('dict.lemmas') }}">
                    {{$key+1}}. <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}{{$args_by_get}}">{{$lemma->lemma}}</a>
                </td>
                <td data-th="{{ trans('dict.lang') }}">
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                <td data-th="{{ trans('dict.pos') }}">
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

