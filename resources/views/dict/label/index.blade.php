<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.labels') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/label/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>

        @include('dict.label._search_form') 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
    <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.visible_values')[1] }}?</th>
                <th colspan="2">{{ trans('messages.in_russian') }}</th>
                <th colspan="2">{{ trans('messages.in_english') }}</th>
                <th>{{ trans('navigation.lemmas') }}</th>                
                @if (User::checkAccess('ref.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($labels as $label)
            <tr>
                <td data-th="{{ trans('messages.sequence_number') }}">
                    {{ $list_count++ }}
                </td>
                <td data-th="{{ trans('dict.visible_values')[1] }}" style="text-align: center">
                    {{ $label->visible ? '+' : '-' }}
                </td>
                <td data-th="{{ trans('dict.name').' '.trans('messages.in_russian') }}">
                    {{ $label->name_ru }}
                </td>
                <td data-th="{{ trans('messages.short').' '.trans('messages.in_russian') }}">
                    {{ $label->short_ru }}
                </td>
                <td data-th="{{ trans('dict.name').' '.trans('messages.in_english') }}">
                    {{ $label->name_en }}
                </td>
                <td data-th="{{ trans('messages.short').' '.trans('messages.in_english') }}">
                    {{ $label->short_en }}
                </td>
                <td data-th="{{ trans('navigation.lemmas') }}" style="text-align: right">
                    @php $lemma_count = $label->lemmaCount(); @endphp
                    @if ($lemma_count) 
                    <a href="{{ route('lemma.index', ['search_label[]'=>$label->id]) }}">{{ format_number($lemma_count) }}</a>
                    @endif
                </td>

                @if (User::checkAccess('ref.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit_small_button', 
                             ['route' => '/dict/label/'.$label->id.'/edit'])
                    @include('widgets.form.button._delete_small_button', ['obj_name' => 'label'])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        {!! $labels->appends($url_args)->render() !!}    
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop

