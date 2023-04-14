<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.districts') }}
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
        
        @include('corpus.district._search_form') 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
    <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('corpus.region') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('navigation.places') }}</th>
                <th>{{ trans('navigation.texts') }}</th>
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
                <td data-th="{{ trans('navigation.places') }}" style="text-align: right">
                    @if($district->places()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/place?search_district='.$district->id) }}">
                        {{ $district->places()->count() }}</a>
                    @else
                        0
                    @endif
                </td>
                <td data-th="{{ trans('navigation.texts') }}" id="text-total-{{$district->id}}" style="text-align: right"></td>
                
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit_small_button', 
                             ['route' => '/dict/district/'.$district->id.'/edit'])
                    @include('widgets.form.button._delete_small_button', ['obj_name' => 'district'])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $districts->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/search.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    @foreach($districts as $district)
        loadCount('#text-total-{{$district->id}}', '{{ LaravelLocalization::localizeURL('/corpus/district/'.$district->id.'/text_count') }}');
    @endforeach
@stop


