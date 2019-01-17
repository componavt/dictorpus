<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:51
 */
$without_enter_form = true;
?>
@extends('layouts.page')

@section('page_title')
{{ trans('auth.registration') }}
@endsection

@section('body')
    {!! Form::open(['class'=>'small-form']) !!}
    @include('widgets.form.formitem._text', ['name' => 'email', 'title' => 'Email', 'attributes'=>['placeholder' => 'Email' ]])
    @include('widgets.form.formitem._password', ['name' => 'password', 'title' => trans('auth.password'), 'placeholder' => trans('auth.password') ])
    @include('widgets.form.formitem._password', ['name' => 'password_confirm', 'title' => trans('auth.password_confirm'), 'placeholder' => trans('auth.password') ])
    @include('widgets.form.formitem._text', ['name' => 'first_name', 'title' => trans('auth.first_name')])
    @include('widgets.form.formitem._text', ['name' => 'last_name', 'title' => trans('auth.last_name') ])
    @include('widgets.form.formitem._submit', ['title' => trans('auth.register')])
    {!! Form::close() !!}
    
    <p><a href='/reset'>{{trans('auth.reset')}}</a></p>
@stop