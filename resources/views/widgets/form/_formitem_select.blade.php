<?php 
if(!isset($value)) 
    $value = null;
if(!isset($values)) 
    $values = array(); 
if(!isset($title)) 
    $title = null;

$add_atributes = ['class'=>'form-control'];

if (isset($multiple) && $multiple) {
    $add_atributes['multiple'] = 'multiple';
}   

if(isset($placeholder)) 
    $add_atributes['placeholder'] = $placeholder;
?>

<div class="form-group {{ $errors->has($name) || $errors->has($name) ? 'has-error' : '' }}">
    @if($title)
	<label for="{{$name}}">{{ $title }}</label>
    @endif
    
	{!! Form::select($name, 
                     $values, 
                     $value,
                     $add_atributes) 
    !!}
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>