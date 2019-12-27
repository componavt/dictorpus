<?php 
if(!isset($value)) {
    $value = [];
} else {
    $value = (array)$value;
}

if(!isset($values)) 
    $values = array(); 

if(!isset($title)) 
    $title = null;

if (!isset($grouped)) {
    $grouped=false;
}

if (!isset($is_multiple) || $is_multiple) {
    $multiple = ' multiple';
} else {
    $multiple = '';
}

if (!isset($class)) {
    $class = 'multiple-select form-control';
}
?>
<div class="form-group {{ $errors->has($name) || $errors->has($name) ? 'has-error' : '' }}
        <?=isset($group_class)  ? ' '.$group_class : '';?>
        "
        <?=isset($id)  ? ' id="'.$id.'"' : '';?>
        <?=isset($style)  ? ' style="'.$style.'"' : '';?>
     >
    @if($title)
    <label for="{{$name}}{{$multiple ? '[]': ''}}">{{ $title }}</label>
    @endif
    
    <select{{ $multiple }} class="{{ $class }}" name="{{ $name }}{{$multiple ? '[]': ''}}" id="{{ $name }}" placeholder="choooose">
        <!--option></option-->
    @if ($grouped)
        @foreach ($values as $group_name=>$group_values)
        <optgroup label="{{$group_name}}">
            @foreach ($group_values as $key=>$val)
                <option value="{{$key}}"<?=(in_array($key,$value)) ? ' selected' : '';?>>{{$val}}</option>
            @endforeach
        </optgroup>
        @endforeach
    @else
        @foreach ($values as $key=>$val)
            <option value="{{$key}}"<?=(in_array($key,$value)) ? ' selected' : '';?>>{{$val}}</option>
        @endforeach
    @endif
    </select>
    
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>
