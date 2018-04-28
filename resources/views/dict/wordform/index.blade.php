<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('navigation.wordforms') }}
@stop

@section('content')
        <h2>{{ trans('navigation.wordforms') }}</h2>
        
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/wordform/with_multiple_lemmas') }}">{{ trans('dict.wordforms_linked_many_lemmas') }}</a></p>
        
        @include('dict.wordform._search_form') 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($wordforms)
        <br>
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.wordform') }}</th>
                <th>{{ trans('dict.gram_attr') }}</th>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.dialect') }}</th>
                @if (User::checkAccess('dict.edit'))
                <th></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($wordforms as $wordform)
                @foreach($wordform['lemmas'] as $key=>$lemma) 
            <tr>
                    @if ($key==0)
                <td rowspan='{{sizeof($wordform['lemmas'])}}'>{{ $list_count++ }}</td>
                <td rowspan='{{sizeof($wordform['lemmas'])}}'>{{$wordform->wordform}}</td>
                    @endif
                <td>
                    <?php 
                    if($wordform->gramset_id) {
                        $gramset = \App\Models\Dict\Gramset::find($wordform->gramset_id);
                        if ($gramset) {
                            print $gramset->gramsetString();
                        }
                    } ?>
                </td>
                <td>
                    @if (sizeof($wordform['lemmas'])>1)
                        {{$key+1}}.
                    @endif
                    <a href="lemma/{{$lemma->id}}{{$args_by_get}}">{{$lemma->lemma}}</a>
                </td>
                <td>
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                    @endif
                </td>
                <td>
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                <td>
                    @if($wordform->dialect_id)
                        {{\App\Models\Dict\Dialect::find($wordform->dialect_id)->name}}
                    @endif
                </td>
                @if (User::checkAccess('dict.edit'))
                <td>
                    @include('widgets.form._button_edit', 
                             ['is_button'=>true, 
                              'route' => '/dict/wordform/'.$wordform->id.'/edit',
                             ])
                </td>
                @endif
            </tr>
                @endforeach
            @endforeach
        </tbody>
        </table>
        @endif
        {!! $wordforms->appends($url_args)->render() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
@stop

