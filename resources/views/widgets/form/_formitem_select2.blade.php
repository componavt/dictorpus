<div class="form-group {{ $errors->has($name) || $errors->has($name) ? 'has-error' : '' }}"
        <?=isset($id)  ? ' id="'.$id.'"' : '';?>
        <?=isset($style)  ? ' style="'.$style.'"' : '';?>
     >
<?php 
if(!isset($value)) 
    $value = [];

if(!isset($values)) 
    $values = array(); 

if(!isset($title)) 
    $title = null;

if (!isset($grouped)) {
    $grouped=false;
}

if (!isset($class)) {
    $class = 'multiple-select form-control';
}
?>
    @if($title)
    <label for="{{$name}}[]">{{ $title }}</label>
    @endif
    
    <select multiple="multiple" class="{{ $class }}" name="{{ $name }}[]">
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
