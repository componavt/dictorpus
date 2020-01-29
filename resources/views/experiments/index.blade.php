@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<H2>Поиск по аналогии</H2>
<div class="row">
    <div class="col-sm-6">
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
    @foreach ($langs as $lang_id => $lang_name)
        <li><a href="experiments/evaluate_search_table?property=pos&search_lang={{$lang_id}}">{{$lang_name}}</a> 
        ({{$totals[$lang_id]['eval_pos_compl_proc']}}%)</li>
    @endforeach
    </ul>
    
    <p>Записать победителя</p>
    <ul>
    @foreach ($langs as $lang_id => $lang_name)
        <li><a href="experiments/write_winners?property=pos&type=end&search_lang={{$lang_id}}">{{$lang_name}}</a> 
        ({{$totals[$lang_id]['pos_win_proc']}}%)</li>
    @endforeach
    </ul>
    
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
    <p>Вывод результатов</p>
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
    </div>
    
    <div class="col-sm-6">
    <h3>грамсетов по множеству словоформ</h3>
@if (User::checkAccess('dict.edit'))
    <p>Сформировать множество словоформ для поиска грамсета</p>
    <ul>
    @foreach ($langs as $lang_id => $lang_name)
        <li><a href="experiments/fill_search_gramset?search_lang={{$lang_id}}">{{$lang_name}}</a> 
        ({{$totals[$lang_id]['total_in_gramset']}})</li>
    @endforeach
    </ul>
    
    <p>Вычислить долю правильных ответов при поиске</p>
    <ul>
        <p>по конечным буквосочетаниям</p>
        <ul>
        @foreach ($langs as $lang_id => $lang_name)
            <li><a href="experiments/evaluate_search_table?property=gramset&search_lang={{$lang_id}}">{{$lang_name}}</a> 
            ({{$totals[$lang_id]['eval_gramset_compl_proc']}}%)</li>
        @endforeach
        </ul><br>
        <p>по псевдоокончаниям</p>
        <ul>
        @foreach ($langs as $lang_id => $lang_name)
            <li><a href="experiments/evaluate_search_gramset_by_affix?search_lang={{$lang_id}}">{{$lang_name}}</a>
            ({{$totals[$lang_id]['eval_gramset_aff_compl_proc']}}%)</li>
        @endforeach
        </ul>
    </ul>
    
    <p>Записать победителя при поиске</p>
    <ul>
        <p>по конечным буквосочетаниям</p>
        <ul>
        @foreach ($langs as $lang_id => $lang_name)
            <li><a href="experiments/write_winners?property=gramset&type=end&search_lang={{$lang_id}}">{{$lang_name}}</a> 
            ({{$totals[$lang_id]['gramset_win_end_proc']}}%)</li>
        @endforeach
        </ul><br>
        <p>по псевдоокончаниям</p>
        <ul>
        @foreach ($langs as $lang_id => $lang_name)
            <li><a href="experiments/write_winners?property=gramset&type=aff&search_lang={{$lang_id}}">{{$lang_name}}</a>
            ({{$totals[$lang_id]['win_aff_proc']}}%)</li>
        @endforeach
        </ul>
    </ul>
    
    <p>Экспортировать таблицу</p>
    <ul>
        <p>САМЫХ ошибочных переходов (==0)</p> 
        <ul>
            <p>в TXT-файл</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift?property=gramset&all=0&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul><br>

            <p>в DOT-файл</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift_to_dot?property=gramset&all=0&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul>
        </ul><br>
    
        <p>ВСЕХ ошибочных переходов (<1)</p>
        <ul>
            <p>в TXT-файл</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift?property=gramset&all=1&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul><br>

            <p>в DOT-файл</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift_to_dot?property=gramset&all=1&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul><br>

            <p>в DOT-файл (кроме единичных случаев, total>1)</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift_to_dot?property=gramset&all=1&total_limit=1&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul><br>

            <p>в DOT-файл c кластерами</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift_to_dot?property=gramset&all=1&with_claster=1&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul>
        </ul>
    </ul>
@endif    
    <p>Вывод результатов</p>
    <ul>
    @foreach ($langs as $lang_id => $lang_name)
        <li><a href="experiments/results_search_gramset?search_lang={{$lang_id}}">{{$lang_name}}</a></li>
    @endforeach
    </ul>
    
    <p>Вывод ошибок</p>
    <ul>
        <p>по конечным буквосочетаниям</p>
        <ul>
        @foreach ($langs as $lang_id => $lang_name)
            <li><a href="experiments/error_list?property=gramset&type=end&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
        @endforeach
        </ul><br>
        
        <p>по псевдоокончаниям</p>
        <ul>
        @foreach ($langs as $lang_id => $lang_name)
            <li><a href="experiments/error_list?property=gramset&type=aff&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
        @endforeach
        </ul>
    </ul>
</div>
@endsection