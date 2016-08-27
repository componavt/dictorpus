@extends('layouts.master')

@section('title')
{{ trans('dict.dialect_list') }}
@stop

@section('content')
        <h2>{{ trans('dict.dialect_list') }}</h2>
            
        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('dict.code') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dialects as $dialect)
            <tr>
                <td>{{$dialect->name_en}}</td>
                <td>{{$dialect->name_ru}}</td>
                <td>{{$dialect->code}}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
@stop


