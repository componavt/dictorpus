<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.corpus_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        <p>
            <a href="/stats/by_corpus">{{ trans('stats.stats_by_corpus') }}</a> |
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/corpus/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/corpus/corpus/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-sm-1">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $search_id,
                'attributes'=>['placeholder' => 'ID']])
    </div>
    <div class="col-sm-9">
         @include('widgets.form.formitem._text', 
                ['name' => 'corpus_name', 
                'value' => $corpus_name,
                'attributes'=>['placeholder' => trans('corpus.name')]])
    </div>
    <div class="col-sm-2" style="text-align: right">       
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>    
        {!! Form::close() !!}

        @include('widgets.founded_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($corpuses as $corpus)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$corpus->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$corpus->name_ru}}</td>
                <td data-th="{{ trans('navigation.texts') }}">
                    @if($corpus->texts)
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}?search_corpus[]={{$corpus->id}}">{{ $corpus->texts()->count() }}</a>
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => '/corpus/corpus/'.$corpus->id.'/edit',
                             ])
                    @include('widgets.form.button._delete', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => 'corpus.destroy', 
                              'args'=>['id' => $corpus->id],
                             ])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


