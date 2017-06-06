<a class='special-symbols-link' type='button' data-for='{{$id_name}}-special'>ä</a>

<div id='{{$id_name}}-special' class='special-symbols'>
    @foreach(['ä','ö','ü','č','š','ž','’'] as $sym)
    <input class='special-symbol-b' type='button' value='{{$sym}}' onClick='insertSymbol("{{$sym}}","{{$id_name}}")'>
    @endforeach
</div>