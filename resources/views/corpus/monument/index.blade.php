<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.monuments') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p>
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/monument/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        @include('corpus.monument._search_form',['url' => '/corpus/monument/']) 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table-bordered table-wide rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('corpus.name') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.dialect') }}</th>
                <th>{{ trans('messages.type') }}</th>
                <th>{{ trans('monument.is_printed') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($monuments as $monument)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('corpus.name') }}">
                    <a href="{{ route('monument.show', $monument->id) }}">{{ $monument->title }}</a>
                </td>
                <td data-th="{{ trans('dict.lang') }}">
                    @if ($monument->lang)
                        {{ $monument->lang->name }}
                    @endif
                </td>
                <td data-th="{{ trans('dict.dialect') }}">
                    @if ($monument->dialect)
                        {{ $monument->dialect->name }}
                    @endif
                </td>
                <td data-th="{{ trans('messages.type') }}">
                    @if ($monument->type_id && isset(trans('monument.type_values')[$monument->type_id]) )
                        {{ trans('monument.type_values')[$monument->type_id] }}
                    @endif
                </td>
                <td data-th="{{ trans('messages.is_printed') }}">
                    @if (isset(trans('monument.is_printed_values')[$monument->is_printed]) )
                        {{ trans('monument.is_printed_values')[$monument->is_printed] }}
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}" style="min-width: 130px; text-align: center">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/monument/'.$monument->id.'/edit'])
                        
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'monument.destroy', 
                             'args'=>['id' => $monument->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $monuments->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    selectWithLang('.select-dialect', "/dict/dialect/list", 'search_lang', '{{ trans('dict.dialect') }}');
@stop


