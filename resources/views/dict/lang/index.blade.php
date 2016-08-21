@extends('layouts.app')

@section('title')
{{ trans('messages.list_languages') }}
@stop

@section('content')
    <div class="container">
        <h2>{{ trans('messages.list_languages') }}</h2>
        
        <table id="languages" class="table">
        <thead>
            <tr>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('messages.lang_code') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($languages as $language)
            <tr>
                <td>{{$language->name_en}}</td>
                <td>{{$language->name_ru}}</td>
                <td>{{$language->code}}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
        
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/dialect') }}">{{ trans('messages.dialect_list') }}</a></p>
                
    </div>
@stop


