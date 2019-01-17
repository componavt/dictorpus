<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:53
 */?>
@extends('layouts.page')

@section('page_title')
{{ trans('auth.password_reset') }}
@endsection

@section('body')
    {!! Form::open(['class'=>'small-form']) !!}
    @include('widgets.form.formitem._text', ['name' => 'email', 'title' => 'Email', 'attributes'=>['placeholder' => trans('auth.your_email')]])
    @include('widgets.form.formitem._submit', ['title' => trans('auth.password_reset')])
    {!! Form::close() !!}
@stop
