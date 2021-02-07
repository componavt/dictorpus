<?php $list_count = 1;?>
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
        <div class="form-flex">
            @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                'value' => $search_name,
                'attributes'=>['placeholder' => trans('corpus.name')]])
            <input class="btn btn-primary btn-default" type="submit" value="{{trans('messages.view')}}">       
        </div>
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table table-striped table-wide wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.name') }}</th>
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
                <td data-th="{{ trans('messages.name') }}">
                    {{$author->name}}
                    {{$author->namesToString() ? '('.$author->namesToString().')' : ''}}
                </td>
                <td data-th="{{ trans('navigation.texts') }}" style="text-align: center">
                    @if($author->texts)
                        {{ $author->texts()->count() }}
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
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


