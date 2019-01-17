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
    {!! Form::radio($name, $value, $value===$checked) !!}
    {{$tail}}
    @endforeach
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>