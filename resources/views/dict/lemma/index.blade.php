<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
@stop

@section('content')
        
        <h1>{{ trans('navigation.lemmas') }}</h1>

        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/sorted_by_length') }}">{{ trans('dict.list_long_lemmas') }}</a> 
            |
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif

        </p>

        @include('dict.lemma.search._lemma_form',['url' => '/dict/lemma/']) 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($numAll)
        <table class="table-bordered table-wide table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('dict.interpretation') }}</th>
                <th>{{ trans('messages.examples') }} *</th>
                @if (User::checkAccess('dict.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
            @foreach($lemmas as $lemma)
            <tr>
                <td>{{ $list_count++ }}</td>
                <td><a href="lemma/{{$lemma->id}}{{$args_by_get}}">{{$lemma->lemma}}</a></td>
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
                        {{$meaning_obj->getMultilangMeaningTextsStringLocale()}}<br>
                    @endforeach
                </td>
                <td>
                    <?php $total_ex = $lemma->countExamples();?>
                    @if ($total_ex)
                        <?php $unchecked = $lemma->countUncheckedExamples();?>
                        {{$lemma->countCheckedExamples()}} /
                        @if ($unchecked >0)
                            <span class="unchecked-count">
                        @endif
                        {{$unchecked}} 
                        @if ($unchecked >0)
                            </span>
                        @endif
                        /
                    @endif
                    {{$lemma->countExamples()}}
                </td>
                @if (User::checkAccess('dict.edit'))
                <td>
                    @include('widgets.form._button_edit', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => '/dict/lemma/'.$lemma->id.'/edit',
                             ])
                    @include('widgets.form._button_delete', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => 'lemma.destroy', 
                              'id' => $lemma->id,
                             ])
                </td>
                @endif
            </tr>
            @endforeach
        </table>
            {!! $lemmas->appends($url_args)->render() !!}
            
            <p><big>*</big> -  {{ trans('dict.example_comment') }}
        @endif

{{--        @include('dict.lemma._modal_delete') --}}
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/lemma');
@stop


