<?php $list_count = 1;?>
@extends('layouts.master')

@section('title')
{{ trans('auth.role_list') }}
@stop

@section('content')
        <h2>{{ trans('auth.role_list') }}</h2>
              
        <p>
        @if (User::checkAccess('corpus.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/role/create') }}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (User::checkAccess('corpus.edit'))
            </a>
        @endif
        </p>

        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('auth.slug') }}</th>
                <th>{{ trans('auth.role_name') }}</th>
                <th>{{ trans('auth.permissions') }}</th>
                <th colspan="2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $list_count++ }}</td>
                <td>{{$role->slug}}</td>
                <td>{{$role->name}}</td>
                <td>{{$role->permissionString()}}</td>
                <td>
                    <a  href="{{ LaravelLocalization::localizeURL('/role/'.$role->id.'/edit') }}" 
                        class="btn btn-warning btn-xs btn-detail" value="{{$role->id}}">{{ trans('messages.edit') }}</a> 
                 </td>
                <td>
                    @include('widgets.form._button_delete', ['is_button'=>true, $route = 'role.destroy', 'id' => $role->id])
                </td>
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


