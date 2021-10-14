@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    <table class="table-bordered table-striped table-wide rwd-table wide-md">
        <tr>
            <th>Заголовок</th>
            <th>Корпус</th>
            <th>Автор</th>
            <th>Комментарий</th>
        </tr>
        @foreach ($texts as $text)
        <tr>
            <td><a href="/ru/corpus/text/{{$text->id}}/edit">{{$text->title}}</a></td>
            <td>{{$text->corpus->name}}</td>
            <td>{{$text->source->author}}</td>
            <td>{{$text->source->comment}}</td>
        </tr>
        @endforeach
    </table>
@endsection