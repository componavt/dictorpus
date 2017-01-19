<?php $short_name_column = 'name_short_'. LaravelLocalization::getCurrentLocale(); ?>
@extends('layouts.master')

@section('title')
{{ trans('dict.pos_list') }}
@stop

@section('content')
        <h2>{{ trans('dict.gram_list') }}</h2>

        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/gram/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>
        
        <table class="table">
        <tbody>
            <tr>
            <?php $column_num = ceil(sizeof($grams)/2);
               $count = 1; ?>
            @foreach($grams as $name => $grams_list)
                <td>
                    <h3>{{ $name }}</h3>
                    @foreach($grams_list as $gramzik)
                    <p>{{ $gramzik->sequence_number }}) 
                        @if (User::checkAccess('ref.edit'))
                            <a href="{{ LaravelLocalization::localizeURL('/dict/gram/'.$gramzik->id) }}">{{ $gramzik->name }}</a> 
                        @else
                            {{ $gramzik->name }}
                        @endif
                        @if ($gramzik->{$short_name_column})
                            ({{ $gramzik->{$short_name_column} }})
                        @endif
                    </p>
                    @endforeach
                </td>
                <?php if ($count==$column_num): ?>
            </tr>
            <tr>
                <?php endif; ?>
                <?php $count ++; ?>
            @endforeach
            </tr>
        </tbody>
        </table>
@stop


