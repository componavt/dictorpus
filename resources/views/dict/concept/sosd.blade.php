<?php $count =1; ?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.sosd') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')    
        <p><b>{{trans('dict.lang')}}</b>: {{$search_lang_name}}
        <table class="table table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th>N</th>
                <th>{{ trans('dict.concept') }}</th>
                @foreach ($place_names as $place_name)
                <th>{{ $place_name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($concept_lemmas as $concept_text => $place_lemmas)
            <tr>
                <td>{{$count++}}.</td>
                <td data-th="{{ trans('dict.concept') }}">{{ $concept_text }}</td>
            @foreach ($place_lemmas as $place_name => $lemmas)
                <td data-th="{{  $place_name }}">
                    <?php $count=0;?>
                    @foreach($lemmas as $lemma_id=>$lemma)
                    <a href="/dict/lemma/{{$lemma_id}}">{{$lemma}}</a>@if($count++<sizeof($lemmas)-1),@endif
                    @endforeach
                </td>
            @endforeach
            </tr>
        @endforeach
        </tbody>
        </table>
    </div>
@stop



