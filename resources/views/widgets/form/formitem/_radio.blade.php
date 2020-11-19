<?php
if (!isset($checked)) { $checked = null; }
if (!isset($values)) { $values = []; }

?>
<div class="{!! $errors->has($name) ? 'has-error' : null !!}">
    @if(isset($title))
	<label for="{{$name}}">{{ $title }}</label>
    @endif
    @foreach ($values as $value=>$tail)
        @if (isset($with_break) && $with_break) 
        <br>
        @endif
        {!! Form::radio($name, $value, $value===$checked) !!}
        {{$tail ?? null}}
    @endforeach
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>