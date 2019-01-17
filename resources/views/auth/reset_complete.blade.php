<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:55
 */?>
@extends('layouts.page')

@section('page_title')
{{ trans('auth.password_recovery') }}
@endsection

@section('content')
    {!! Form::open(['class'=>'small-form']) !!}
    @include('widgets.form.formitem._password', ['name' => 'password', 'title' => trans('auth.password'), 'placeholder' => trans('auth.password') ])
    @include('widgets.form.formitem._password', ['name' => 'password_confirm', 'title' => trans('auth.password_confirm'), 'placeholder' => trans('auth.password') ])
    @include('widgets.form.formitem._submit', ['title' => trans('auth.confirm')])
    {!! Form::close() !!}
@stop