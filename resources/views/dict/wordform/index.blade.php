<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.wordforms') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/wordform/with_multiple_lemmas') }}">{{ trans('dict.wordforms_linked_many_lemmas') }}</a></p>
        
        @include('dict.wordform._search_form') 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($wordforms)
        <br>
        <table class="table-bordered table-wide table-striped rwd-table wide-lg">
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
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($wordforms as $wordform)
                @foreach($wordform['lemmas'] as $key=>$lemma) 
            <tr>
                    @if ($key==0)
                <td data-th="No" rowspan='{{sizeof($wordform['lemmas'])}}'>{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.wordform') }}" rowspan='{{sizeof($wordform['lemmas'])}}'>{{$wordform->wordform}}</td>
                    @endif
                <td data-th="{{ trans('dict.gram_attr') }}">
                    <?php 
                    if($wordform->gramset_id) {
                        $gramset = \App\Models\Dict\Gramset::find($wordform->gramset_id);
                        if ($gramset) {
                            print $gramset->gramsetString();
                        }
                    } ?>
                </td>
                <td data-th="{{ trans('dict.lemmas') }}">
                    @if (sizeof($wordform['lemmas'])>1)
                        {{$key+1}}.
                    @endif
                    <a href="lemma/{{$lemma->id}}{{$args_by_get}}">{{$lemma->lemma}}</a>
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                    @endif
                </td>
                <td data-th="{{ trans('dict.lang') }}">
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                <td data-th="{{ trans('dict.dialect') }}">
                    @if($wordform->dialect_id)
                        {{\App\Models\Dict\Dialect::find($wordform->dialect_id)->name}}
                    @endif
                </td>
                @if (User::checkAccess('dict.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
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

