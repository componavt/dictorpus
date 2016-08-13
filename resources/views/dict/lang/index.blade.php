@extends('layouts.app')

@section('title')
List of languages
@stop

@section('content')

    <h2>Languages</h2>
    <table id="languages" class="table">
    <thead>
        <tr>
            <th>English</th>
            <th>Russian</th>
            <th>Language code</th>
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
@stop


