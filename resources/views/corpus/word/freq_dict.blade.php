<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.word_frequency') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        {!! Form::open(['url' => '/corpus/word/freq_dict', 'method' => 'get']) !!}
        <div class="row">
            <div class="col-md-4">
                @include('widgets.form.formitem._select', 
                        ['name' => 'search_lang', 
                         'values' => $lang_values,
                         'value' => $url_args['search_lang'],
                         'title' => trans('dict.lang'),
                ])                 
            </div>
            <div class="col-md-4">
                @include('widgets.form.formitem._text', 
                        ['name' => 'search_word', 
                         'special_symbol' => true,
                         'value' => $url_args['search_word'],
                         'title'=> trans('corpus.word')
                        ])

            </div>
            <div class="col-md-4 search-button-b">       
                <span>
                {{trans('messages.show_by')}}
                </span>
                @include('widgets.form.formitem._text', 
                        ['name' => 'limit_num', 
                        'value' => $url_args['limit_num'], 
                        'attributes'=>['placeholder' => trans('messages.limit_num') ]]) 
                <span>
                        {{ trans('messages.records') }}
                </span>
                @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
            </div>
        </div>
        {!! Form::close() !!}

        @if ($words)
        <table class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('corpus.word') }}</th>
                <th>{{ trans('messages.frequency') }}</th>
                <th>{{ trans('corpus.has_link_with_lemma') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($words as $word)
            <?php
            $link_to_texts = '/corpus/text?search_lang%5B%5D='.$url_args['search_lang'].'&search_word='.$word->l_word;
            ?>
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('corpus.word') }}">
                    <a href="{{ LaravelLocalization::localizeURL($link_to_texts) }}">
                        {{$word->l_word}}
                    </a>
                </td>
                <td data-th="{{ trans('messages.frequency') }}">{{$word->frequency}}</td>
                <td data-th="{{ trans('corpus.has_link_with_lemma') }}">
                    @if($word->isLinkedWithLemmaByLang($url_args['search_lang']))
                    +
                    @else
                    <span class="warning">---</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
    </div>
@stop


