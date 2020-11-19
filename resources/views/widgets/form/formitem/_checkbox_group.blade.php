<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:45
  * Updated: 24.08.2016 by Nataly Krizhanovsky
*/?>
<?php
if(!isset($checked)) 
    $checked = null;
if(!isset($values)) 
    $values = array(); 
if(! isset($title)) 
    $title = null;
?>
<div class="form-group {{ $errors->has($name) || $errors->has($name) ? 'has-error' : '' }}">
    @if($title)
        <label for="{{$name}}">{{ $title }}</label>
    @endif
    
    @foreach($values as $value=>$t)
        {!! Form::checkbox($name, $value, in_array($value,$checked)) !!}
	{{ $t }}<br>
    @endforeach 
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>