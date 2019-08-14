<?php $list_count = $limit_num * ($page-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('corpus.district_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p>
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/corpus/district/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>
        
        {!! Form::open(['url' => '/corpus/district/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $search_id,
                'attributes'=>['size' => 3,
                               'placeholder' => 'ID']])
         @include('widgets.form.formitem._text', 
                ['name' => 'district_name', 
                'value' => $district_name,
                'attributes'=>['size' => 15,
                               'placeholder' => trans('corpus.name')]])
        @include('widgets.form.formitem._select', 
                ['name' => 'region_id', 
                 'values' => $region_values,
                 'value' => $region_id,
                 'attributes' => ['placeholder' => trans('corpus.region')]]) 
        <br>         
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
        
        {{trans('messages.show_by')}}
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $limit_num, 
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num')]]) {{ trans('messages.records') }}
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>
        
        <table class="table rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('corpus.region') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('navigation.places') }}</th>
                @if (User::checkAccess('corpus.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($districts as $district)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('corpus.region') }}">{{$district->region->name}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$district->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$district->name_ru}}</td>
                <td data-th="{{ trans('navigation.places') }}">
                    @if($district->places)
                        {{ $district->places()->count() }}
                    @endif
                </td>
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/corpus/district/'.$district->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => 'district.destroy', 
                             'args'=>['id' => $district->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $districts->appends(['limit_num' => $limit_num,
                             'district_name' => $district_name,
                             'region_id'=>$region_id])->render() !!}

    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/district');
@stop


