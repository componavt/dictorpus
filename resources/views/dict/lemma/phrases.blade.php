<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.phrases') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p>
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif

        </p>

        @include('dict.lemma.search._phrases_form',['url' => '/dict/lemma/phrases']) 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($numAll)
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.interpretation') }}</th>
                <th>{{ trans('dict.lemmas') }}</th>
                @if (User::checkAccess('dict.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
            @foreach($lemmas as $lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lemma') }}"><a href="{{LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id)}}{{$args_by_get}}">{{$lemma->lemma}}</a></td>
                <td data-th="{{ trans('dict.lang') }}">
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                <td data-th="{{ trans('dict.interpretation') }}">
                    @foreach ($lemma->meanings as $meaning_obj) 
                        {{$meaning_obj->getMultilangMeaningTextsString(LaravelLocalization::getCurrentLocale())}}<br>
                    @endforeach
                </td>
                <td data-th="{{ trans('dict.lemmas') }}">
                    {!! $lemma->phraseLemmasListWithLink() !!}
                </td>
                @if (User::checkAccess('dict.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => '/dict/lemma/'.$lemma->id.'/edit',
                             ])
                    @include('widgets.form.button._delete', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => 'lemma.destroy', 
                              'args'=>['id' => $lemma->id]
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
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


