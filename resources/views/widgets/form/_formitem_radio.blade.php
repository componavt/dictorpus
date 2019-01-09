<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:45
  * Updated: 24.08.2016 by Nataly Krizhanovsky
*/?>
<?php
if(! isset($checked)) $checked = null;
if(! isset($title)) $title = null;
if(! isset($values)) $values = [];

?>
<div class="{!! $errors->has($name) ? 'has-error' : null !!}">
    @if($title)
	<label for="{{$name}}">{{ $title }}</label>
    @endif
    @foreach ($values as $value=>$tail)
    {!! Form::radio($name, $value, $value==$checked) !!}
    {{$tail}}
    @endforeach
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>