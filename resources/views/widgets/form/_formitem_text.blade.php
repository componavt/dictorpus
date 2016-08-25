<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:41
 * Updated: 24.08.2016 by Nataly Krizhanovsky
 */?>
<?php 
if(!isset($value)) 
    $value = null;
if(!isset($title)) 
    $title = null;
if(!isset($placeholder)) 
    $placeholder = null;
?>
<div class="form-group {!! $errors->has($name) ? 'has-error' : null !!}">
    @if($title)
	<label for="{{$name}}">{{ $title }}</label>
    @endif
    {!! Form::text($name, $value, array('placeholder' =>  $placeholder )) !!}
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>