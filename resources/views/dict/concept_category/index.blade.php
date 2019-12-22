@extends('layouts.page')

@section('page_title')
{{ trans('navigation.concept_categories') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        <p style="text-align:right">
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/concept_category/create') }}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif
        </p>
        
        <table class="table table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th>{{ trans('messages.code') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                @if (User::checkAccess('dict.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($concept_categories as $concept_category)
            <tr>
                <td data-th="{{ trans('messages.code') }}">{{$concept_category->id}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$concept_category->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$concept_category->name_ru}}</td>
                @if (User::checkAccess('dict.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', ['is_button'=>true, 'route' => '/dict/concept_category/'.$concept_category->id.'/edit'])
                    @include('widgets.form.button._delete', ['is_button'=>true, $route = 'concept_category.destroy', 'args'=>['id' => $concept_category->id]])
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


