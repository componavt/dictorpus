@extends('layouts.master')

@section('title')
{{ trans('dict.list_languages') }}
@stop

@section('content')
        <h2>{{ trans('dict.list_languages') }}</h2>
        
        <p style="text-align:right">
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lang/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif
        </p>
        
        <table id="languages" class="table-striped table">
        <thead>
            <tr>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('dict.lang_code') }}</th>
                @if (User::checkAccess('dict.edit'))
                <th></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($languages as $language)
            <tr>
                <td>{{$language->name_en}}</td>
                <td>{{$language->name_ru}}</td>
                <td>{{$language->code}}</td>
                @if (User::checkAccess('dict.edit'))
                <td>
                    @include('widgets.form._button_edit', ['is_button'=>true, 'route' => '/dict/lang/'.$language->id.'/edit'])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/dialect') }}">{{ trans('dict.dialect_list') }}</a></p>
@stop


