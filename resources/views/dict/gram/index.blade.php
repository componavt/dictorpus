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
        
        <?php $column_num = 4;
           $count = 1; ?>
        <div class="row">
        @foreach($grams as $name => $grams_list)
            <div class="col-sm-3">
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
                    @if (User::checkAccess('ref.edit'))
                        @if ($gramzik->conll)
                            ({{ $gramzik->conll }})
                        @endif
                        @include('widgets.form.button._edit', ['route' => '/dict/gram/'.$gramzik->id.'/edit', 'without_text' => 1])                        
                    @endif
                </p>
                @endforeach
            </div>
            <?php if ($count==$column_num): ?>
        </div>
        <div class="row">
            <?php endif; ?>
            <?php $count++; ?>
        @endforeach
        </div>
@stop


