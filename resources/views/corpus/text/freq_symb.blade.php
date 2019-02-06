<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.text_frequency') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        {!! Form::open(['url' => '/corpus/text/freq_symb', 'method' => 'get', 'class'=>'inline']) !!}
        <div class="row">
            <div class="col-md-6">
                @include('widgets.form.formitem._select', 
                        ['name' => 'search_lang', 
                         'values' => $lang_values,
                         'value' => $url_args['search_lang'],
                         'title' => trans('dict.lang'),
                ])                 
            </div>
            <div class="col-md-6 submit-button-b"><br>       
                @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
            </div>
        </div>
        {!! Form::close() !!}

        @if ($symbols)
        <table class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('corpus.symbol') }}</th>
                <th>code</th>
                <th>{{ trans('messages.frequency') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($symbols as $symbol=>$frequency)
            <?php
//dd($symbol, $frequency, (string)$url_args['search_lang']);               
                $link_to_texts = '/corpus/text?search_lang%5B%5D='.(string)$url_args['search_lang'][0].'&search_text='.$symbol;
            ?>
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('corpus.symbol') }}">
                    <a href="{{ LaravelLocalization::localizeURL($link_to_texts) }}" style='text-decoration: none'>
                        {{$symbol}}
                    </a>
                </td>
                <td data-th="{{ trans('corpus.symbol') }}">
                    <a href="{{ LaravelLocalization::localizeURL($link_to_texts) }}" style='text-decoration: none'>
                        {{mb_ord($symbol)}}
                    </a>
                </td>
                <td data-th="{{ trans('messages.frequency') }}">{{$frequency}}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
    </div>
@stop


