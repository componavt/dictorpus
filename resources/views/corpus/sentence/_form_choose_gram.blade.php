<input type='hidden' id='insertGramTo' value=''>
<div class="choose-gram row">
    @foreach ($gram_values as $gc_id => $gc)
    <div class="col-md-4 gram-category" id='gc_{{$gc_id}}'>
        <h2>{{$gc[0]}}</h2>
        @foreach ($gc[1] as $code => $name)
        <p><input type="checkbox" value="{{$code}}" id='gram_{{$code}}'> {{$name}}</p>
        @endforeach
    </div>
    @endforeach
</div>
