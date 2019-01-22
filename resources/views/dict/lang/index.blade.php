@extends('layouts.page')

@section('page_title')
{{ trans('dict.list_languages') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        <p style="text-align:right">
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lang/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif
        </p>
        
        <table id="languages" class="table-striped table rwd-table wide-md">
        <thead>
            <tr>
                <th>{{ trans('messages.seq_num') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('dict.lang_code') }}</th>
                @if (User::checkAccess('dict.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($languages as $language)
            <tr>
                <td data-th="{{ trans('messages.seq_num') }}">{{$language->sequence_number}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$language->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$language->name_ru}}</td>
                <td data-th="{{ trans('dict.lang_code') }}">{{$language->code}}</td>
                @if (User::checkAccess('dict.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', ['is_button'=>true, 'route' => '/dict/lang/'.$language->id.'/edit'])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/dialect') }}">{{ trans('dict.dialect_list') }}</a></p>
@stop


