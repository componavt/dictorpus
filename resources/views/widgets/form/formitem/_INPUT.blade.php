<?php     
$classes = 'form-control'. (!empty($class) ? ' '.$class : '');
$id_name = preg_replace("/[\.\]\[]/","_",$name);
?>
<div class="form-group {!! $errors->has($name) ? 'has-error' : null !!}">
    @if(isset($title) && $title)
    <label for="{{ $name }}">{{ $title }}</label>
    @endif
    
    <input class="{{ $classes }}" type="{{ $type }}" {!! $func ?? '' !!}
           name="{{ $name }}" id="{{ $id_name }}" value="{{ $value ?? old($name) }}"
           placeholder="{{ $placeholder ?? null }}" pattern="{{ $pattern ?? null }}"
        @if (!empty($required))
           required
        @endif
    >
    
    @if (isset($field_comments))
    <span class='field_comments'>{{$field_comments}}</span>
    @endif
    
    @if (isset($help_func) && $help_func) 
    <i class='help-icon far fa-question-circle fa-lg' onClick='{{$help_func}}'></i>
    @endif
    
    {{ $tail ?? '' }}                                    
    <p class="help-block">{!! $errors->first($name) !!}</p>
</div>