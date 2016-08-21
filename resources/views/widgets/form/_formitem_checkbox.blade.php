<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:45
 */?>
<?php
if(! isset($value)) $value = null;
if(! isset($checked)) $checked = null;
if(! isset($title)) $title = null;
?>
<div class="{!! $errors->has($name) ? 'has-error' : null !!}">
    <label for="{!! $name !!}">{{ $title }}</label>
    {!! Form::checkbox($name, $value, $checked) !!}
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>