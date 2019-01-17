<?php 
if(!isset($value)) 
    $value = null;
if(!isset($values)) 
    $values = []; 
if(!isset($title)) 
    $title = null;

if (!isset($attributes)) {
    $attributes = [];
}

if (!isset($attributes['class'])) {
    $attributes['class'] = 'form-control';
}
$attributes['id'] = $name;

?>

<div class="form-group {{ $errors->has($name) || $errors->has($name) ? 'has-error' : '' }}">
    @if($title)
	<label for="{{$name}}">{{ $title }}</label>
    @endif
    
	{!! Form::select($name, 
                     $values, 
                     $value,
                     $attributes) 
        !!}
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>