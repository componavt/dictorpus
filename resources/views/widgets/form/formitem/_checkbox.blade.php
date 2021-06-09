<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:45
  * Updated: 24.08.2016 by Nataly Krizhanovsky
*/?>
<?php
if(! isset($value)) $value = null;
if(! isset($checked)) $checked = null;
if(! isset($title)) $title = null;
if(! isset($tail)) $tail = null;
$attributes['id'] = $name;
?>
<div class="{!! $errors->has($name) ? 'has-error' : null !!}">
    @if($title)
	<label for="{{$name}}">{{ $title }}</label>
    @endif
    {!! Form::checkbox($name, $value, $checked, $attributes) !!}
    @if($tail)
	<label for="{{$name}}" style='font-weight: normal'>{{ $tail }}</label>
    @endif
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>