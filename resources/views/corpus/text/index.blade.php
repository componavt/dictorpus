<?php $list_count = $limit_num * ($page-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('corpus.text_list') }}
@stop

@section('content')
        <h2>{{ trans('corpus.text_list') }}</h2>
        
        <p>
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
                ['name' => 'text_title', 
                'value' => $text_title,
                'size' => 15,
                'placeholder' => trans('corpus.title')])
        @include('widgets.form._formitem_select', 
                ['name' => 'lang_id', 
                 'values' => $lang_values,
                 'value' => $lang_id,
                 'placeholder' => trans('corpus.select_lang') ]) 
        @include('widgets.form._formitem_select', 
                ['name' => 'corpus_id', 
                 'values' => $corpus_values,
                 'value' => $corpus_id,
                 'placeholder' => trans('corpus.select_corpus') ]) 
                 
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        
        {{trans('messages.show_by')}}
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $limit_num, 
                'size' => 5,
                'placeholder' => trans('messages.limit_num') ]) {{ trans('messages.records') }}
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lang') }}</th>
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
                <td>{{$text->corpus->name}}</td>
                <td><a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}">{{$text->title}}</td>
                <td>
                    @if ($text->transtext)
                    {{$text->transtext->title}}
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td>
                    <a  href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'/edit') }}" 
                        class="btn btn-warning btn-xs btn-detail" value="{{$text->id}}">{{ trans('messages.edit') }}</a>
                 </td>
                <td>
                    @include('widgets.form._button_delete', ['is_button'=>true, $route = 'text.destroy', 'id' => $text->id])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $texts->appends(['limit_num' => $limit_num,
                             'text_title' => $text_title,
                             'lang_id'=>$lang_id,
                             'corpus_id'=>$corpus_id])->render() !!}

    </div>
@stop


