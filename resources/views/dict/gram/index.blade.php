<?php $short_name_column = 'name_short_'. LaravelLocalization::getCurrentLocale(); ?>
@extends('layouts.page')

@section('page_title')
{{ trans('dict.gram_list') }}
@stop

@section('body')
    <p style="text-align: right">
    @if (User::checkAccess('ref.edit'))
        <a href="{{ LaravelLocalization::localizeURL('/dict/gram/create') }}">
    @endif
        {{ trans('messages.create_new_m') }}
    @if (User::checkAccess('ref.edit'))
        </a>
    @endif
    </p>
        
    <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>LGR</th>
                <th>Unimorph</th>
                <th>CONLL</th>
                @if (User::checkAccess('ref.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
    @foreach($grams as $name => $grams_list)
            <tr><td colspan="6" style="font-weight: bold">{{ $name }}</td></tr>
        @foreach($grams_list as $gramzik)
            <tr>
                <td data-th="No">{{ $gramzik->sequence_number }}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{ $gramzik->name_en }}</td>
                <td data-th="{{ trans('messages.in_russian') }}">
                    @if (User::checkAccess('ref.edit'))
                        <a href="{{ LaravelLocalization::localizeURL('/dict/gram/'.$gramzik->id) }}">{{ $gramzik->name }}</a> 
                    @else
                        {{ $gramzik->name }}
                    @endif
                </td>
                <td data-th="LGR">{{ $gramzik->lgr }}</td>
                <td data-th="Unimorph">{{ $gramzik->unimorph }}</td>
                <td data-th="CONLL">{{ $gramzik->conll }}</td>
            @if (User::checkAccess('ref.edit'))
                <td data-th="{{ trans('messages.actions') }}">            
                        @include('widgets.form.button._edit', ['route' => '/dict/gram/'.$gramzik->id.'/edit', 'without_text' => 1])    
                </td>
            @endif
        @endforeach
    @endforeach
        </tbody>
    </table>
@stop


