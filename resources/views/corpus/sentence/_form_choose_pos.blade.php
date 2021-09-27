<input type='hidden' id='insertPosTo' value=''>
<div class="choose-pos row">
    @foreach ($pos_values as $code => $name)
    <div class="col-md-4">
        <input type="checkbox" value="{{$code}}" id='pos_{{$code}}'> {{$name}}
    </div>
    @endforeach
</div>
