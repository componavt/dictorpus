<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:38
 */?>
@extends('layouts.master')
@section('content')
    {!! Form::open() !!}
        @include('widgets.form._formitem_text', ['name' => 'email', 'title' => 'Email', 'placeholder' => trans('auth.your_email') ])
        @include('widgets.form._formitem_password', ['name' => 'password', 'title' => trans('auth.password'), 'placeholder' => trans('auth.password') ])
        @include('widgets.form._formitem_checkbox', ['name' => 'remember', 'title' => trans('auth.remember')] )
        @include('widgets.form._formitem_btn_submit', ['title' => trans('auth.login')])
    {!! Form::close() !!}

    <br><p><a href="{{URL::to('/reset')}}">{{ trans('auth.reset') }}</a></p>
@stop