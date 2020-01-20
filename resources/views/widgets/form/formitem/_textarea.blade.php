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
if(!isset($attributes['class'])) 
    $attributes['class'] = 'form-control';
if(!isset($special_symbol)) 
    $special_symbol = false;

$id_name = preg_replace("/[\.\]\[]/","_",$name);
$attributes['id'] = $id_name;
?>
<div class="form-group {!! $errors->has($name) ? 'has-error' : null !!}">
    @if($title)
	<label for="{{$name}}">{{ $title }}</label>
        <span class='imp'>{!!@isset($help_text) ? $help_text : ''!!}</span>
    @endif
    {!! Form::textarea($name, $value, $attributes) !!}
    @if ($special_symbol) 
        @include('dict.special_symbols',['id_name'=>$id_name])
    @endif
    <p class="help-block">
        {!! $errors->first($name) !!}
    </p>
</div>