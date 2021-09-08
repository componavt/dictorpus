<a class='special-symbols-link' type='button' data-for='{{$id_name}}-special'>ä</a>

<div id='{{$id_name}}-special' class='special-symbols'>
    @foreach(['ä'=>'','ö'=>'','ü'=>'','č'=>'','š'=>'','ž'=>'','’'=>'', 
    '|'=>'разделить предложение', '['=>'левая скобка шаблона', ']'=>'правая скобка шаблона', 
    '–'=>'', '¦'=>'разделить слово', '^'=>'соединить предложения'/*, 'V̱'=>'любая гласная', 'C̱'=>'любая согласная'*/] 
        as $sym=>$sym_title)
    <input class='special-symbol-b' title='{{$sym_title}}' type='button' value='{{$sym}}' onClick='insertSymbol("{{$sym}}","{{$id_name}}")'>
    @endforeach
</div>