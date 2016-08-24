<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:53
 */?>
@extends('layouts.master')
@section('content')
    {!! Form::open() !!}
    @include('widgets.form._formitem_text', ['name' => 'email', 'title' => 'Email', 'placeholder' => trans('auth.your_email') ])
    @include('widgets.form._formitem_btn_submit', ['title' => trans('auth.password_confirm')])
    {!! Form::close() !!}
@stop