<div class="form-group {{ $errors->has($name) || $errors->has($name) ? 'has-error' : '' }}">
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
?>
    @if($title)
    <label for="{{$name}}">{{ $title }}</label>
    @endif
    
    <select multiple="multiple" class="<?=$attributes['class']?>" name="<?=$name?>[]">
    <?php foreach ($values as $key=>$value): ?>
        <option value="<?=$key?>"><?=$value?></option>
    <?php endforeach;?>
    </select>
    
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>
