<?php $list_count = $limit_num * ($page-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('dict.dialect_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('content')
        <h1>{{ trans('dict.dialect_list') }}</h1>
            
        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/dialect/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>

        @include('dict.dialect._search_form',['url' => '/dict/dialect/']) 

        @include('widgets.founded_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
    <table class="table-bordered table-wide rwd-table wide-lg">
        <thead>
            <tr>
                <th>{{ trans('messages.sequence_number') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('dict.code') }}</th>
                <th>{{ trans('dict.wordforms') }}</th>                
                <th>{{ trans('navigation.texts') }}</th>                
                @if (User::checkAccess('ref.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($dialects as $dialect)
            <tr>
                <td data-th="{{ trans('messages.sequence_number') }}">{{$dialect->sequence_number}}</td>
                <td data-th="{{ trans('dict.lang') }}">{{$dialect->lang->name}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$dialect->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$dialect->name_ru}}</td>
                <td data-th="{{ trans('dict.code') }}">{{$dialect->code}}</td>
                <td data-th="{{ trans('dict.wordforms') }}">
                    <a href='{{ LaravelLocalization::localizeURL('/dict/wordform?search_dialect='.$dialect->id) }}'>
                        {{$dialect->wordforms()->count()}}
                    </a>
                </td>
                <td data-th="{{ trans('navigation.texts') }}">
                    <a href='{{ LaravelLocalization::localizeURL('/corpus/text?search_dialect='.$dialect->id) }}'>
                        {{$dialect->texts()->count()}}
                    </a>
                </td>

                @if (User::checkAccess('ref.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'url_args' => $url_args,
                             'route' => '/dict/dialect/'.$dialect->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                            'url_args' => $url_args,
                            'route' => 'dialect.destroy', 
                            'args'=>['id' => $dialect->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $dialects->appends(['limit_num' => $limit_num,
                                           'lang_id'=>$lang_id])->render() !!}    
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop

