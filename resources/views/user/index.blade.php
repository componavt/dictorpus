<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('auth.user_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
        <table class="table-bordered table-wide table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th>No</th>
                <th>E-mail</th>
                <th>{{ trans('auth.name') }}</th>
                <th>{{ trans('auth.city') }} / {{ trans('auth.affilation') }}</th>
                <th>{{ trans('auth.roles') }} / {{ trans('navigation.langs') }}</th>
                <th>{{ trans('auth.last_activity') }}</th>
                @if (User::checkAccess('user.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="E-mail">{{$user->email}}</td>
                <td data-th="{{ trans('auth.name') }}">{{$user->first_name}} {{$user->last_name}}</td>
                <td data-th="{{ trans('auth.city') }} / {{ trans('auth.affilation') }}">
                    {{$user->country}}@if ($user->city)
                    , {{$user->city}}
                    @endif
                    @if ($user->affilation)
                    , {{$user->affilation}}
                    @endif
                </td>
                <td data-th="{{ trans('auth.roles') }} / {{ trans('navigation.langs') }}">
                    {{$user->rolesNames()}}
                    @if ($user->langString())
                    <br>{{$user->langString()}}
                    @endif
                </td>
                <td data-th="{{ trans('auth.last_activity') }}">
                    {{$user->last_login}}
                    @if ($user->getLastActionTime())
                    <br>{{$user->getLastActionTime()}}
                    @endif
                </td>
                @if (User::checkAccess('user.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => '/user/'.$user->id.'/edit',
                             ])
                    @include('widgets.form.button._delete', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => 'user.destroy', 
                              'id' => $user->id,
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
    recDelete('{{ trans('messages.confirm_delete') }}', '/corpus/user');
@stop


