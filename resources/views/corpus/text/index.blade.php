<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('corpus.text_list') }}
@stop

@section('content')
        <h2>{{ trans('corpus.text_list') }}</h2>
        
        <p style="text-align:right">
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/corpus/text/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_text', 
                ['name' => 'search_title', 
                 'special_symbol' => true,
                'value' => $url_args['search_title'],
                'attributes'=>['size' => 15,
                               'placeholder' => trans('corpus.title')]])
                               
        @include('widgets.form._formitem_select', 
                ['name' => 'search_lang', 
                 'values' => $lang_values,
                 'value' => $url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.select_lang')] ])
                 
        @include('widgets.form._formitem_select', 
                ['name' => 'search_corpus', 
                 'values' => $corpus_values,
                 'value' => $url_args['search_corpus'],
                 'attributes'=>['placeholder' => trans('corpus.select_corpus') ]]) 
                                  
        @include('widgets.form._formitem_select', 
                ['name' => 'search_dialect', 
                 'values' =>$dialect_values,
                 'value' =>$url_args['search_dialect'],
                 'attributes'=>['placeholder' => trans('dict.select_dialect') ]]) 
        <br>         
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        
        {{trans('messages.show_by')}}
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) {{ trans('messages.records') }}
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.dialect') }}</th>
                <th>{{ trans('corpus.corpus') }}</th>
                <th>{{ trans('corpus.title') }}</th>
                <th>{{ trans('messages.translation') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th colspan="2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($texts as $text)
            <tr>
                <td>{{ $list_count++ }}</td>
                <td>{{$text->lang->name}}</td>
                <td>
                    @if($text->dialects)
                        @foreach ($text->dialects as $dialect)
                        {{$dialect->name}}<br>
                        @endforeach
                        
                    @endif
                </td>
                <td>{{$text->corpus->name}}</td>
                <td><a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}">{{$text->title}}</td>
                <td>
                    @if ($text->transtext)
                    {{$text->transtext->title}}
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td>
                    @include('widgets.form._button_edit', ['is_button'=>true, 'route' => '/corpus/text/'.$text->id.'/edit'])
                 </td>
                <td>
                    @include('widgets.form._button_delete', ['is_button'=>true, $route = 'text.destroy', 'id' => $text->id])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $texts->appends($url_args)->render() !!}
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/text');
@stop
