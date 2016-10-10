<?php $list_count = 1;?>
@extends('layouts.master')

@section('title')
{{ trans('auth.user_list') }}
@stop

@section('content')
        <h2>{{ trans('auth.user_list') }}</h2>
              
        <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>E-mail</th>
                <th>{{ trans('auth.first_name') }}</th>
                <th>{{ trans('auth.last_name') }}</th>
                <th>{{ trans('auth.permissions') }}</th>
                <th>{{ trans('auth.roles') }}</th>
                <th>{{ trans('auth.last_login') }}</th>
                <th colspan="2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $list_count++ }}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->first_name}}</td>
                <td>{{$user->last_name}}</td>
                <td>{{$user->permissionString()}}</td>
                <td>{{$user->rolesNames()}}</td>
                <td>{{$user->last_login}}</td>
                <td>
                    <a  href="{{ LaravelLocalization::localizeURL('/user/'.$user->id.'/edit') }}" 
                        class="btn btn-warning btn-xs btn-detail" value="{{$user->id}}">{{ trans('messages.edit') }}</a> 
                 </td>
                <td>
                    @include('widgets.form._button_delete', ['is_button'=>true, $route = 'user.destroy', 'id' => $user->id])
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
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/user');
@stop


