<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('auth.role_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <p>
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/role/create') }}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>

        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('auth.slug') }}</th>
                <th>{{ trans('auth.role_name') }}</th>
                <th>{{ trans('auth.permissions') }}</th>
                @if (User::checkAccess('user.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('auth.slug') }}">{{$role->slug}}</td>
                <td data-th="{{ trans('auth.role_name') }}">{{$role->name}}</td>
                <td data-th="{{ trans('auth.permissions') }}">{{$role->permissionString()}}</td>
                @if (User::checkAccess('user.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => '/role/'.$role->id.'/edit',
                             ])
                    @include('widgets.form.button._delete', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => 'role.destroy', 
                              'id' => $role->id,
                             ])
                </td>
                @endif
            </tr> 
            @endforeach
        </tbody>
        </table>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/role');
@stop


