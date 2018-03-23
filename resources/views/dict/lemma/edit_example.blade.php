<?php
    $styles = [
        '10'=>'relevance-10',
        '7'=>'relevance-7',
        '5'=>'relevance-5',
        '3'=>'relevance-3',
        ];
?>
@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.lemmas') }}</h1>
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_example')}}</h2>
        <p>@include('dict.lemma.show.example_sentence', ['relevance'=>'', 'count'=>''])</p>

        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a></p>
        

        {!! Form::model($lemma, array('method'=>'POST', 'route' => array('lemma.update.examples', $lemma->id))) !!}
        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])

        <table class="table lemma-example-edit">
            @foreach ($meanings as $meaning)
            <tr>
                <td>
                    <h3>{{$meaning->lemma->lemma}}</h3>
                    {{$meaning->meaning_n}} {{trans('dict.meaning')}}
                    @if (isset($meaning_texts[$meaning->id]))
                        @foreach ($meaning_texts[$meaning->id] as $lang_name => $meaning_text)
                        <p><b>{{$lang_name}}:</b> {{$meaning_text}}</p>
                        @endforeach
                    @endif
                </td>
                <td>
                    <?php $relevance = isset($meaning->relevance) 
                                        ? $meaning->relevance
                                        : 1;
                    ?>
                    @include('widgets.form._formitem_select',
                            ['name' => 'relevance['.$meaning->id.'_'.$sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id'].']',
                             'values' => trans('dict.relevance_scope'),
                             'value' => $relevance,
                            ])
                </td>
            </tr>
            @endforeach
        </table>
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.save')])
        {!! Form::close() !!}
@stop
