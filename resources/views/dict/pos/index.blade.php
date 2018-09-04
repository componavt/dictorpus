@extends('layouts.page')

@section('page_title')
{{ trans('dict.pos_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <table class="table">
        <tbody>
            <tr>
            @foreach($pos_category as $category => $parts_of_speech)
                <td>
                    <h3>{{ trans('dict.pos_category_'.$category) }}</h3>
                    @foreach($parts_of_speech as $pos)
                    <p>{{ $pos->name }} ({{ $pos->code }})</p>
                    @endforeach
                </td>
            @endforeach
            </tr>
        </tbody>
        </table>
@stop


