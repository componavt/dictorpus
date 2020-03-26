@extends('layouts.page')

@section('page_title')
{{ trans('navigation.sosd') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        <table class="table table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th>{{ trans('dict.concept') }}</th>
                @foreach ($place_names as $place_name)
                <th>{{ $place_name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($concept_lemmas as $concept_text => $place_lemmas)
            <tr>
                <td data-th="{{ trans('dict.concept') }}">{{ $concept_text }}</td>
            @foreach ($place_lemmas as $place_name => $lemmas)
                <td data-th="{{  $place_name }}">{{$lemmas}}</td>
            @endforeach
            </tr>
        @endforeach
        </tbody>
        </table>
    </div>
@stop



