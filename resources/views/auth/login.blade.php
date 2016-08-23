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
        @include('widgets.form._formitem_text', ['name' => 'email', 'title' => 'Email', 'placeholder' => 'Ваш Email' ])
        @include('widgets.form._formitem_password', ['name' => 'password', 'title' => 'Пароль', 'placeholder' => 'Пароль' ])
        @include('widgets.form._formitem_checkbox', ['name' => 'remember', 'title' => 'Запомнить меня'] )
        @include('widgets.form._formitem_btn_submit', ['title' => 'Вход'])
    {!! Form::close() !!}
<p><a href="{{URL::to('/reset')}}">Забыли пароль?</a></p>
@stop