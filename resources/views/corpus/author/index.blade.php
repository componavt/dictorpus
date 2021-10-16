<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.authors') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p style="text-align:right">
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/author/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/corpus/author/', 
                             'method' => 'get']) 
        !!}
        <div class="row">
            <div class="col-sm-8">
            @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                'value' => $url_args['search_name'],
                'attributes'=>['placeholder' => trans('messages.name')]])
            </div>
            @include('widgets.form._search_div', ['cols'=>4])
        </div>
        {!! Form::close() !!}

        @include('widgets.founded_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table table-striped table-wide wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('corpus.in_native') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($authors as $author)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('messages.in_english') }}">
                    {{$author->name_en}}
                </td>
                <td data-th="{{ trans('messages.in_russian') }}">
                    {{$author->name_ru}}
                </td>
                <td data-th="{{ trans('corpus.in_native') }}">
                    {{$author->namesToString()}}
                </td>
                <td data-th="{{ trans('navigation.texts') }}" style="text-align: center">
                    @if($author->texts()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text?search_author=')}} {{$author->id}}">{{ $author->texts()->count() }}</a>
                    @else
                    0
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}" style="text-align: center">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/author/'.$author->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'author.destroy', 
                             'args'=>['id' => $author->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $authors->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


