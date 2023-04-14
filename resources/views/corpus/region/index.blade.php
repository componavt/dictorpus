<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.regions') }}
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
        
        @include('corpus.region._search_form') 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
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
            @foreach($regions as $region)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$region->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$region->name_ru}}</td>
                <td data-th="{{ trans('navigation.places') }}" style="text-align: right">
                    @if($region->places()->count())
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/place?search_region='.$region->id) }}">
                        {{ $region->places()->count() }}</a>
                    @else
                        0
                    @endif
                </td>
                <td data-th="{{ trans('navigation.texts') }}" id="text-total-{{$region->id}}" style="text-align: right"></td>
                
                @if (User::checkAccess('corpus.edit'))
                <td data-th="{{ trans('messages.actions') }}"  style="text-align: center">
                    @include('widgets.form.button._edit_small_button', 
                             ['route' => '/corpus/region/'.$region->id.'/edit'])
                    @include('widgets.form.button._delete_small_button', ['obj_name' => 'region'])
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
    {!!Html::script('js/search.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    @foreach($regions as $region)
        loadCount('#text-total-{{$region->id}}', '{{ LaravelLocalization::localizeURL('/corpus/region/'.$region->id.'/text_count') }}');
    @endforeach
@stop


