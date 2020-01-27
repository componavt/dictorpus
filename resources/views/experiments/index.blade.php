@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<H2>Поиск по аналогии</H2>
<div class="row">
    <div class="col-sm-6">
    <h3>части речи среди лемм и словоформ</h3>
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
    <p>Вывод результатов</p>
    <ul>
    @foreach ($langs as $lang_id => $lang_name)
        <li><a href="experiments/results_search_pos?search_lang={{$lang_id}}">{{$lang_name}}</a></li>
    @endforeach
    </ul>
    </div>
    
    <div class="col-sm-6">
    <h3>грамсетов по множеству словоформ</h3>
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

            <p>в DOT-файл c кластерами</p>
            <ul>
            @foreach ($langs as $lang_id => $lang_name)
                <li><a href="experiments/export_error_shift_to_dot?property=gramset&all=1&with_claster=1&search_lang={{$lang_id}}">{{$lang_name}}</a></li>
            @endforeach
            </ul>
        </ul>
    </ul>
    
    <p>Вывод результатов</p>
    <ul>
    @foreach ($langs as $lang_id => $lang_name)
        <li><a href="experiments/results_search_gramset?search_lang={{$lang_id}}">{{$lang_name}}</a></li>
    @endforeach
    </ul>
</div>
@endsection