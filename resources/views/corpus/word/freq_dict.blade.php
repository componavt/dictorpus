<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.word_frequency') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        @include('corpus.word._search_form',['url' => '/corpus/word/freq_dict']) 

        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}">
                {{ trans('dict.create_new_lemma') }}
            </a>
        @endif
        
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
                @if ($list_count<=$url_args['limit_num'])
            <?php
                $link_to_texts = '/corpus/text?search_lang%5B%5D='.$url_args['search_lang'].'&search_word='.$word->word;
                $is_linked = $word->isLinkedWithLemmaByLang($url_args['search_lang']);
//dd($url_args);               
            ?>
                    @if ($url_args['search_linked']===NULL || $url_args['search_linked']=="1" && $is_linked || $url_args['search_linked']=="0" && !$is_linked)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('corpus.word') }}">
                    <a href="{{ LaravelLocalization::localizeURL($link_to_texts) }}">
                        {{$word->word}}
                    </a>
                </td>
                <td data-th="{{ trans('messages.frequency') }}">{{$word->frequency}}</td>
                <td data-th="{{ trans('corpus.has_link_with_lemma') }}">
                    @if($is_linked)
                    +
                    @else
                    <span class="warning">---</span>
                    @endif
                </td>
            </tr>
                    @endif
                @endif
            @endforeach
        </tbody>
        </table>
        @endif
    </div>
@stop


