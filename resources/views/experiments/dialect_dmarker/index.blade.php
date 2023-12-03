@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Определение диалектной принадлежности</h2>
    
    @include('widgets.form.formitem._textarea', 
            ['name' => 'text', 
             'rows' => 15,
             'counter' => 2000,
             'title'=> 'Введите текст' ]) 
    <div class='row' style='margin-bottom: 20px'>
        <div class="col-sm-4">
            <input class="btn btn-primary btn-default" type="button" 
                   onclick="dialectGuess('{{ LaravelLocalization::getCurrentLocale() }}')" 
                   value="определить диалект">
        </div>
        <div class="col-sm-8">
            <div id="results" style="color: #ef3928; font-size: 24px;"></div>
            <div><img class="img-loading" id="loading-text" src="{{ asset('images/loading.gif') }}"></div>
        </div>
    </div>    
    
    <ul>
        <li><a href="/experiments/dialect_dmarker/frequencies">таблица с частотами</a></li>
        <li><a href="/experiments/dialect_dmarker/fractions">таблица с относительными частотами</a></li>
        <!--li><a href="/experiments/dialect_dmarker/words">посмотреть слова</a></li-->
        <li><a href="/experiments/dialect_dmarker/compare_freq_SSindex">сравнить относительные частоты и индекс Шепли-Шубика</a></li>
        <li><a href="/experiments/dialect_dmarker/check_results">результаты проверки эксперимента</a></li>
    </ul>
    @if (User::checkAccess('admin'))
    <ul>
        <li><a href="/experiments/dialect_dmarker/calculate">переписать частоты</a></li>
        <li><a href="/experiments/dialect_dmarker/calculate_coalitions">переcчитать коалиции</a></li>
        <li><a href="/experiments/dialect_dmarker/calculate_SSindex">переcчитать индексы Шепли-Шубика</a></li>
        <li><a href="/experiments/dialect_dmarker/check">проверка эксперимента</a></li>
    </ul>
    @endif
@endsection

@section('footScriptExtra')
    {!!Html::script('js/text.js')!!}
    {!!Html::script('js/form.js')!!}
@endsection

@section('jqueryFunc')
    limitTextarea("#text");
@endsection