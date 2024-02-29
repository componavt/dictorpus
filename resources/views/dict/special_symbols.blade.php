<?php
    $symb_list = ['ä'=>'','ö'=>'','ü'=>'','č'=>'','š'=>'','ž'=>'','’'=>''];
    if (!isset($full_special_list) || $full_special_list) {
        $symb_list['|'] = 'разделить предложение';
        $symb_list['['] = 'левая скобка шаблона'; 
        $symb_list[']'] = 'правая скобка шаблона'; 
        $symb_list['–'] = '–';
        $symb_list['¦'] = 'разделить слово';
        $symb_list['^'] = 'соединить предложения';/*, 'V̱'=>'любая гласная', 'C̱'=>'любая согласная'*/
    }
?>
<a class='special-symbols-link' onClick="toggleSpecialJS(this)" type='button' data-for='{{$id_name}}-special'>{{isset($special_sym) ? $special_sym : ''}}ä</a>

<div id='{{$id_name}}-special' class='special-symbols'>
    <div class="special-symbols-header">
        <div class="special-symbols-close">
            <i class="fa fa-times" onclick="closeSpecial('{{$id_name}}-special')"></i>
        </div>
    </div>
    <div class="special-symbols-body">
    @foreach($symb_list as $sym=>$sym_title)
    <input class='special-symbol-b' title='{{$sym_title}}' type='button' value='{{$sym}}' onClick='insertSymbol("{{$sym}}","{{$id_name}}")'>
    @endforeach
    </div>
</div>