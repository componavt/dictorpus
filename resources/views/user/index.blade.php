<?php $list_count = 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('auth.user_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
    <ul class="nav nav-tabs">
        @foreach($roles as $role_id => $role_name)
        <li data-toggle="tab" href="#role{{$role_id}}"
            @if($role_id==2)  class="active"
            @endif><a href="#">{{$role_name}}</a></li>
        @endforeach
    </ul>
    <div class="tab-content">
    @foreach($roles as $role_id => $role_name)
        @include('user._role_users', ['class' => 'tab-pane fade'.($role_id==2 ? ' in active' : '')])
    @endforeach
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


