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
$id_name = preg_replace("/[\.\]\[]/","_",$name);
$attributes['id'] = $id_name;

?>

<div class="form-group {{ $errors->has($name) || $errors->has($name) ? 'has-error' : '' }}">
    @if($title)
	<label for="{{$name}}">
            {{ $title }}
            @if (isset($call_add_onClick)) 
            <i onClick="{{$call_add_onClick}}" class="call-add fa fa-plus fa-lg" title="{{$call_add_title ?? ''}}"></i>
            @endif
        </label>
    @endif
    
	{!! Form::select($name, 
                     $values, 
                     $value,
                     $attributes) 
        !!}
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>