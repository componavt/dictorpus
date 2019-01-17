<?php 
if(!isset($value)) 
    $value = null;
if(!isset($values)) 
    $values = array(); 
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
    
    <select id='{{$name}}' name='{{$name}}' class='{{$attributes['class']}}'>
        @foreach ($values as $k=>$v)
        <option value='{{$k}}' 
            <?php if($k === $value) { print ' selected';} ?>
                >
            <span
            <?php if(isset($styles[$k])) { print " class='".$styles[$k]."'";} ?> 
            >{{$v}}</span>
        </option>
        @endforeach
    </select>
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>