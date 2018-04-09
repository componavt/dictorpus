<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('navigation.omonyms') }}
@stop

@section('content')
        <h2>{{ trans('navigation.omonyms') }}</h2>

        {!! Form::open(['url' => '/dict/lemma/omonyms',
                             'method' => 'get',
                             'class' => 'form-inline'])
        !!}
        @include('widgets.form._formitem_text',
                ['name' => 'search_lemma',
                 'special_symbol' => true,
                'value' => $url_args['search_lemma'],
                'attributes'=>['size' => 15,
                               'placeholder'=>trans('dict.lemma')]])
                               
        @include('widgets.form._formitem_select',
                ['name' => 'search_lang',
                 'values' =>$lang_values,
                 'value' => $url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]])
                 
        @include('widgets.form._formitem_select',
                ['name' => 'search_pos',
                 'values' =>$pos_values,
                 'value' => $url_args['search_pos'],
                 'attributes'=>['placeholder' => trans('dict.select_pos') ]]) 
                 
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])

        {{trans('messages.show_by')}}
        @include('widgets.form._formitem_text',
                ['name' => 'limit_num',
                'value' => $url_args['limit_num'],
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) {{ trans('messages.records') }}
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($lemmas)
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.interpretation') }}</th>
            </tr>
        </thead>
            @foreach($lemmas as $lemma)
            <tr>
                <td>{{ $list_count++ }}</td>
                <td>{{ $lemma->lang->name }}</td>
                <td>{{ $lemma->pos->name }}</td>
                <td><a href="/dict/lemma/{{$lemma->id}}{{$args_by_get}}">{{$lemma->lemma}}</a></td>
                <td>
                    @foreach ($lemma->meanings as $meaning_obj) 
                        {{$meaning_obj->getMultilangMeaningTextsString(LaravelLocalization::getCurrentLocale())}}<br>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </table>
            {!! $lemmas->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop