<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:16
 */?>

@if ($errors->any())
    <div class="alert alert-danger alert-block">
        {{-- <button type="button" class="close" data-dismiss="alert"><i class="fa fa-minus-square"></i></button> --}}
        <strong>{{ trans('error.error') }}</strong>:
        @if ($message = $errors->first(0, ':message'))
            {{ $message }}
        @else
            {{ trans('error.check_form') }}
        @endif
    </div>
@endif

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-minus-square"></i></button>
        <strong>{{ trans('error.success') }}</strong>: {{ $message }}
    </div>
@endif
