<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('navigation.phrases') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('content')
        
        <h2>{{ trans('navigation.phrases') }}</h2>

        <p>
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif

        </p>

        @include('dict.lemma._search_form',['url' => '/dict/lemma/phrases',
                                            'is_search_id' => 0,
                                            'is_search_pos' => 0,
                                            'is_search_wordform' => 0
                                           ]) 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($numAll)
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('dict.interpretation') }}</th>
                <th>{{ trans('dict.lemmas') }}</th>
                @if (User::checkAccess('dict.edit'))
                <th colspan="2"></th>
                @endif
            </tr>
        </thead>
            @foreach($lemmas as $lemma)
            <tr>
                <td>{{ $list_count++ }}</td>
                <td><a href="{{LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id)}}{{$args_by_get}}">{{$lemma->lemma}}</a></td>
                <td>
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                <td>
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                        @if ($lemma->reflexive)
                            ({{ trans('dict.reflexive_verb') }})
                        @endif
                    @endif
                </td>
                <td>
                    @foreach ($lemma->meanings as $meaning_obj) 
                        {{$meaning_obj->getMultilangMeaningTextsString(LaravelLocalization::getCurrentLocale())}}<br>
                    @endforeach
                </td>
                <td>
                    {!! $lemma->phraseLemmasListWithLink() !!}
                </td>
                @if (User::checkAccess('dict.edit'))
                <td>
                    @include('widgets.form._button_edit', 
                             ['is_button'=>true, 
                              'route' => '/dict/lemma/'.$lemma->id.'/edit',
                             ])
                </td>
                <td>
                    @include('widgets.form._button_delete', 
                             ['is_button'=>true, 
                              'route' => 'lemma.destroy', 
                              'id' => $lemma->id,
                             ])
                </td>
                @endif
            </tr>
            @endforeach
        </table>
            {!! $lemmas->appends($url_args)->render() !!}
            
        @endif

@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/lemma');
@stop


