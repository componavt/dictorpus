<?php
    $styles = [
        '10'=>'relevance-10',
        '7'=>'relevance-7',
        '5'=>'relevance-5',
        '3'=>'relevance-3',
        ];
?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
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
        <input type="hidden" name="back_to_url" value="{{$back_to_url}}">

        <table class="table lemma-example-edit">
            <tr>
                <th>{{trans('corpus.sentences')}}</th>
                
                @foreach ($meanings as $meaning)
                <td class="lemma-example-edit-right">
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
                    @include('dict.lemma.example.sentence')
                </td>
                @foreach ($meanings as $meaning)
                <td>
                    @include('widgets.form.formitem._select',
                            ['name' => 'relevance['.$meaning->id.'_'.$sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id'].']',
                             'values' => trans('dict.relevance_scope'),
                             'value' => $sentence['relevance'][$meaning->id] ?? 1,
                            ])
                </td>
                @endforeach
            </tr>
            @endforeach
        </table>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.save')])
        {!! Form::close() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/text.js')!!}
@stop

@section('jqueryFunc')
    showLemmaLinked();    
@stop
