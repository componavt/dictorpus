<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.dialects') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/dialect/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>

        @include('dict.dialect._search_form') 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
    <table class="table-bordered table-wide table-striped rwd-table wide-md">
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
                <td data-th="{{ trans('dict.wordforms') }}" id="wordform-total-{{$dialect->id}}" style="text-align: right"></td>
                <td data-th="{{ trans('navigation.texts') }}" id="text-total-{{$dialect->id}}" style="text-align: right"></td>

                @if (User::checkAccess('ref.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit_small_button', 
                             ['route' => '/dict/dialect/'.$dialect->id.'/edit'])
                    @include('widgets.form.button._delete_small_button', ['obj_name' => 'dialect'])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $dialects->appends($url_args)->render() !!}    
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/search.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    @foreach($dialects as $dialect)
        loadCount('#text-total-{{$dialect->id}}', '{{ LaravelLocalization::localizeURL('/dict/dialect/'.$dialect->id.'/text_count') }}');
        loadCount('#wordform-total-{{$dialect->id}}', '{{ LaravelLocalization::localizeURL('/dict/dialect/'.$dialect->id.'/wordform_count') }}');
    @endforeach
@stop

