    <h3>части речи среди лемм и словоформ</h3>
@if (User::checkAccess('dict.edit'))
    <p>Сформировать множество лемм и словоформ для поиска части речи</p>
    <ul>
    @foreach ($langs as $lang_id => $lang_name)
        <li><a href="experiments/fill_search_pos?search_lang={{$lang_id}}">{{$lang_name}}</a> 
        ({{$totals[$lang_id]['total_in_pos']}})</li>
    @endforeach
    </ul>
    
    <p>Вычислить долю правильных ответов</p>
    <ul>
        <p>по самым длинным конечным буквосочетаниям</p>
        <ul>
        @foreach ($langs as $lang_id => $lang_name)
            <li><a href="experiments/evaluate_search_table?property=pos&search_lang={{$lang_id}}">{{$lang_name}}</a> 
            ({{$totals[$lang_id]['eval_pos_compl_proc']}}%)</li>
        @endforeach
        </ul><br>
        <p>по всем конечным буквосочетаниям</p>
        <ul>
        @foreach ($langs as $lang_id => $lang_name)
            <li><a href="experiments/evaluate_search_table?property=pos&all=1&search_lang={{$lang_id}}">{{$lang_name}}</a> 
            ({{$totals[$lang_id]['evals_pos_compl_proc']}}%)</li>
        @endforeach
        </ul>        
    </ul>
    
    <!--p>Записать победителя</p>
    <ul>
    @foreach ($langs as $lang_id => $lang_name)
        <li><a href="experiments/write_winners?property=pos&type=end&search_lang={{$lang_id}}">{{$lang_name}}</a> 
        ({{$totals[$lang_id]['pos_win_proc']}}%)</li>
    @endforeach
    </ul-->
    
    <p>Экспортировать таблицу</p>
    <ul>
        <p>САМЫХ ошибочных переходов (==0)</p> 
        <ul>
            <p>в TXT-файл</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift?property=pos&all=0&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul><br>
            <p>в DOT-файл</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift_to_dot?property=pos&all=0&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul>
        </ul><br>
    
        <p>ВСЕХ ошибочных переходов (<1)</p>
        <ul>
            <p>в TXT-файл</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift?property=pos&all=1&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul><br>

            <p>в DOT-файл</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift_to_dot?property=pos&all=1&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul>
        </ul>
    </ul>
@endif    
    <p><a href="experiments/results_search?property=pos">Вывод результатов</a></p>
    <ul>
    @foreach ($langs as $lang_id => $lang_name)
        <li><a href="experiments/results_search_pos?search_lang={{$lang_id}}">{{$lang_name}}</a></li>
    @endforeach
    </ul>
    
    <p>Вывод ошибок</p>
    <ul>
    @foreach ($langs as $lang_id => $lang_name)
        <li><a href="experiments/error_list?property=pos&type=end&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
    @endforeach
    </ul>
    
    <p>Проверить результат</p>
    @foreach ($langs as $lang_id => $lang_name)
        @include('experiments.word_check_form',['property'=>'pos', 'search_lang'=>$lang_id, 'submit_value'=>$lang_name])
    @endforeach
