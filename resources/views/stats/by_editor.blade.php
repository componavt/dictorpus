@extends('layouts.page')

@section('page_title')
{{ trans('stats.stats_by_editors') }}
@endsection

@section('headExtra')
    {!!Html::style('css/stats.css')!!}
@stop

@section('body')
    <table class="table-bordered stats-table">
        <tr>
            <th>Модель</th>
            <th>Атрибут</th>
            <th>Количество</th>
        </tr>
        @foreach ($history as $editor)
        <tr>
            <td><a href="{{ route('stats.by_editor', $editor) }}">{{ $editor->name }}</a></td>
            <td>{{ $editor->count }}</td>
            <td>{{ $editor->last_time }}</td>
        </tr>
        @endforeach
    </table>
@stop
