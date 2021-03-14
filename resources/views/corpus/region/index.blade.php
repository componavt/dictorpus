<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.region_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p style="text-align:right">
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/region/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/corpus/region/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $search_id,
                'attributes'=>['size' => 3,
                               'placeholder' => 'ID']])
         @include('widgets.form.formitem._text', 
                ['name' => 'region_name', 
                'value' => $region_name,
                'attributes'=>['size' => 15,
                               'placeholder' => trans('corpus.name')]])
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
        {!! Form::close() !!}

        @include('widgets.founded_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table-striped table-wide rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('navigation.places') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($regions as $region)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$region->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$region->name_ru}}</td>
                <td data-th="{{ trans('navigation.places') }}">
                    @if($region->places)
                        {{ $region->places()->count() }}
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/region/'.$region->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'region.destroy', 
                             'args'=>['id' => $region->id]])
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


