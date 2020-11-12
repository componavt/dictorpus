@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <h2>Помощь</h2>
    
    <h3>Работа редактора с размеченным текстом</h3>
    <p>При создании/редактировании текста происходит разметка текста (разбиение на предложения, слова) и автоматический поиск совпадения слов с леммами и словоформами в словаре на языке текста.</p>
    <p>Если есть перевод на другой язык, то выполнено выравнивание текста по предложениям: при наведении курсора на текст, в переводном тексте предложение выделяется
        <span style="background-color: yellow">желтым</span> цветом  
        и наоборот при наведении курсора на переводной текст, в предложение тексте выделяется <span style="background-color: rgb(169, 239, 248)">голубым</span> цветом.</p>
    <!--div class="figure">
        <div class='row'>
            <div class="col-sm-6">
            <img src="/images/help/text_alignment1.png">
            </div>            
            <div class="col-sm-6">
            <img src="/images/help/text_alignment2.png">
            </div>
        </div>
        <span class="figure-caption">Рис.1 </span>
    </div-->
    <p>Слова в тексте выделены разным цветом:</p>
    <ul>
        <li><span class="lemma-linked meaning-not-checked gramset-not-checked">синие</span> - найдено одно значение и возможно грамсеты, ни то, ни другое не проверено экспертом;</li>
        <li><span class="lemma-linked meaning-checked gramset-not-checked">голубые</span> - найдено одно значение и грамсеты, что-то проверено экспертом;</li>
        <li><span class="lemma-linked polysemy">красные</span> - найдено несколько значений; </li>
        <li><span class="lemma-linked meaning-checked no-gramsets">зеленые</span> - значение и возможно грамсет проверены и подтверждены экспертом; </li>
        <li>черные - нет соответствия в словаре.</li>
    </ul>

    <div class='row'>
        <div class="col-sm-4">
        <h3>Выбор значения леммы</h3>
        <p>По щелчку на <span class="lemma-linked meaning-not-checked gramset-not-checked">синее</span>,
             <span class="lemma-linked meaning-checked gramset-not-checked">голубое</span> или 
             <span class="lemma-linked polysemy">красное</span> слово под ним открывается панель с найденными значениями, 
             где эксперт может выбрать правильное значение и грамсет, нажав на плюс 
             или перейти к форме редактирования на отдельной странице, нажав на карандаш (Рис. 2).</p> 
        
        <p>По щелчку на <span class="lemma-linked meaning-checked no-gramsets">зеленое</span> слово открывается панель с подтвержденным значением и 
            ссылкой на форму редактирования на отдельной странице (на случай ошибочного выбора значения) (Рис. 1).</p>
        
            <div class="figure">
                <img src="/images/help/result_of_wordform_creation.png">
                <span class="figure-caption">Рис.1 После сохранения формы значение считается проверенным и подтвержденным экспертом</span>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="figure">
                <img src="/images/help/select_meaning_in_text2.png">
                <span class="figure-caption">Рис.2 Выбор значения для слова Putui  (найдено несколько значений), всплывающее панель с вариантами значений и лемм, а также грамматических характеристик словоформ, автоматически извлечённых из словаря VepKar</span>
            </div>
        </div>
    </div>

    <div class='row'>
        <div class="col-sm-4">
            <h3>Добавление новой словоформы</h3>
            <p>По щелчку на чёрное слово открывается окошко в котором можно выбрать лемму, значение, грамматические признаки и диалекты, в котором употребляется эта словоформа. 
                Таким образом эксперт теперь может выполнить несколько операций: 
                создать новую словоформу с грамматическими признаками у существующей леммы, 
                создать связь значения леммы и слова в тексте (Рис.3). 
                После сохранения окошко закрывается, исходное слово переходит в статус проверенного: окрашивается в 
                <span class="lemma-linked meaning-checked no-gramsets">зеленый</span> цвет и у него появляется панель с выбранным значением (Рис. 1).</p>
        </div>
        <div class="col-sm-8">
            <div class="figure">
                <img src="/images/help/wordform_creation_from_text.png">
                <span class="figure-caption">Рис.3 Привязка слова к значению леммы и создание словоформы</span>
            </div>
        </div>
    </div>
    
    
@stop
