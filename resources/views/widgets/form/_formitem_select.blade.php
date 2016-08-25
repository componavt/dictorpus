<?php 
if(!isset($value)) 
    $value = null;
if(!isset($values)) 
    $values = array(); 
if(!isset($title)) 
    $title = null;
if(!isset($placeholder)) 
    $placeholder = null;
?>

<div class="form-group {{ $errors->has($name) || $errors->has($name) ? 'has-error' : '' }}">
    @if($title)
	<label for="{{$name}}">{{ $title }}</label>
    @endif
    
	{!! Form::select($name, 
                     $values, 
                     $value,
                     array('placeholder'=>$placeholder, 
                           'class'=>'form-control')) 
    !!}
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>