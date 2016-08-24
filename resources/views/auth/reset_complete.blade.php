<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:55
 */?>
@extends('layouts.master')
@section('content')
    {!! Form::open() !!}
    @include('widgets.form._formitem_password', ['name' => 'password', 'title' => trans('auth.password'), 'placeholder' => trans('auth.password') ])
    @include('widgets.form._formitem_password', ['name' => 'password_confirm', 'title' => trans('auth.password_confirm'), 'placeholder' => trans('auth.password') ])
    @include('widgets.form._formitem_btn_submit', ['title' => trans('auth.confirm')])
    {!! Form::close() !!}
@stop