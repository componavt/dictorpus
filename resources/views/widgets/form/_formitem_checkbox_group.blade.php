<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:45
  * Updated: 24.08.2016 by Nataly Krizhanovsky
*/?>
<?php
if(! isset($value)) {
    $values = [];
} elseif(!is_array($value)) {
    $values[$value] = $title;
} else {
    $values = $value;
}   
if(! isset($checked)) $checked = null;
if(! isset($title)) $title = null;
?>
<div class="{!! $errors->has($name) ? 'has-error' : null !!}">
    @foreach($values as $value=>$title)
        @if($title)
	<label for="{{$name}}">{{ $title }}</label>
        @endif
    {!! Form::checkbox($name, $value, $checked) !!}
    @endforeach 
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>