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
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_lemma')}}: {{ $lemma->lemma}}</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a></p>
        
        <p><b>{{ trans('dict.lang') }}:</b> {{ $lemma->lang->name}}</p>
        <p><b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}</p>

        <h3>{{ trans('messages.examples') }}</h3>

<?php $limit = 100; 
      $sentences = $lemma->sentences($limit);
//dd($sentences);      
      $count=1; ?>
        
        @if (sizeof($sentences))

        {!! Form::model($lemma, array('method'=>'POST', 'route' => array('lemma.update.examples', $lemma->id))) !!}
        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])

        <table class="table lemma-example-edit">
            <tr>
                <th>{{trans('corpus.sentences')}}</th>
                
                @foreach ($meanings as $meaning)
                <td>
                    {{$meaning->meaning_n}} {{trans('dict.meaning')}}
                    @if (isset($meaning_texts[$meaning->id]))
                        @foreach ($meaning_texts[$meaning->id] as $lang_name => $meaning_text)
                        <p><b>{{$lang_name}}:</b> {{$meaning_text}}</p>
                        @endforeach
                    @endif
                </td>
                @endforeach
            </tr>
            @foreach ($sentences as $sentence)
            <tr>
                <td>
                    {{ $count++ }}.
                    @include('dict.lemma.show.example_sentence')
                </td>
                @foreach ($meanings as $meaning)
                <td>
                    <?php $relevance = isset($sentence['relevance'][$meaning->id]) 
                                        ? $sentence['relevance'][$meaning->id]
                                        : 1;
                    ?>
                    @include('widgets.form._formitem_select',
                            ['name' => 'relevance['.$meaning->id.'_'.$sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id'].']',
                             'values' => trans('dict.relevance_scope'),
                             'value' => $relevance,
                            ])
{{--                    @include('widgets.form._formitem_select_styled',
                            ['name' => 'relevance['.$meaning->id.'_'.$sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id'].']',
                             'values' => trans('dict.relevance_scope'),
                             'value' => $relevance,
                             'styles' => $styles
                            ]) --}}
                </td>
                @endforeach
            </tr>
            @endforeach
        </table>
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.save')])
        {!! Form::close() !!}
        @endif
@stop
