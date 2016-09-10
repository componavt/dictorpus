<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:51
 */?>
@extends('layouts.master')
@section('content')
    {!! Form::open(['class'=>'small-form']) !!}
    @include('widgets.form._formitem_text', ['name' => 'email', 'title' => 'Email', 'attributes'=>['placeholder' => 'Email' ]])
    @include('widgets.form._formitem_password', ['name' => 'password', 'title' => trans('auth.password'), 'placeholder' => trans('auth.password') ])
    @include('widgets.form._formitem_password', ['name' => 'password_confirm', 'title' => trans('auth.password_confirm'), 'placeholder' => trans('auth.password') ])
    @include('widgets.form._formitem_text', ['name' => 'first_name', 'title' => trans('auth.first_name')])
    @include('widgets.form._formitem_text', ['name' => 'last_name', 'title' => trans('auth.last_name') ])
    @include('widgets.form._formitem_btn_submit', ['title' => trans('auth.register')])
    {!! Form::close() !!}
@stop