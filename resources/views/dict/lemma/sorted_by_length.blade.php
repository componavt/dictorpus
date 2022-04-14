<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('dict.list_long_lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        {!! Form::open(array('url' => '/dict/lemma/sorted_by_length', 
                             'method' => 'get', 
                             'class' => 'form-inline')) 
        !!}
        {!! Form::text('limit_num', 
                       $url_args['limit_num'], 
                       array('placeholder'=>trans('messages.limit_num'), 
                             'class'=>'form-control', 
                             'required'=>'true')) 
        !!} 
        {!! Form::submit(trans('messages.view'),
                               array('class'=>'btn btn-default btn-primary')) 
                !!}
                {!! Form::close() !!}<br>

        @include('widgets.found_records', ['numAll'=>$numAll])

        @if ($lemmas)
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.pos') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lemma') }}"><a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}/{{$lemma->id}}{{$args_by_get}}">{{$lemma->lemma}}</a></td>
                <td data-th="{{ trans('dict.lang') }}">
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                <td data-th="{{ trans('dict.lemma') }}">
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
        {!! $lemmas->appends(['limit_num' => $url_args['limit_num']])->render() !!}
@stop


