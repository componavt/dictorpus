@extends('layouts.'.($for_print ? 'for_print' : 'page'))

@section('page_title')
{{ trans('collection.name_list')[9] }}
@stop

@section('headExtra')
    {!!Html::style('css/text.css')!!}
@stop

@section('body')
    <p>
        <a href="{{ LaravelLocalization::localizeURL('/corpus/collection/9') }}">{{trans('collection.to_collection')}}</a>
    </p>

    <h4 style="text-align:right">Л.И. Иванова</h4>
    <h3>Указатель основных сюжетов и мотивов/тем карельских мифологических рассказов (быличек)<br>
    Часть 1. Мифологические существа, связанные с праздниками</h3>

    <h4>I. Сюндю (Syndy)</h4>
    <p><b>A.</b> Сюндю – это</p>
    <ol>
        <li>Черт 
            132/72а, 
            <a href="{{ route('text.show', 9288) }}">3431/32</a>, 
            <a href="{{ route('text.show', 9307) }}">3458/17</a></li>
        <li>Водяной 
            2945/3, 
            <a href="{{ route('text.show', 9315) }}">2945/7</a>, 
            <a href="{{ route('text.show', 9320) }}">3463/27-35</a>, 
            <a href="{{ route('text.show', 9305) }}">2949/3</a></li>
        <li>Нечто плохое 
            <a href="{{ route('text.show', 9294) }}">3265/74-76</a></li>
        <li>Бог 
            <a href="{{ route('text.show', 9265) }}">3064/14</a>, 
            <a href="{{ route('text.show', 9309) }}">3065/18</a>, 
            <a href="{{ route('text.show', 9289) }}">3433/52-53</a>, 
            <a href="{{ route('text.show', 9271) }}">3459/17</a></li>
        <li>Бог или человек в лесу 
            <a href="{{ route('text.show', 9309) }}">3065/18</a></li>
    </ol>

    <p><b>B.</b> Появление Сюндю в виде</p>
    <ol>
        <li>Копна сена 
            132/91а, 
            <a href="{{ route('text.show', 9297) }}">701/4</a>, 
            <a href="{{ route('text.show', 9298) }}">1734/8</a>, 
            2357/13, 
            <a href="{{ route('text.show', 9283) }}">3265/58-60</a>, 
            <a href="{{ route('text.show', 9294) }}">3265/74-76</a>, 
            <a href="{{ route('text.show', 9304) }}">3363/15</a>, 
            <a href="{{ route('text.show', 9303) }}">3429/20</a>, 
            <a href="{{ route('text.show', 9320) }}">3463/27-35</a></li>
        <li>Куча сена, при этом невод вместо портянок, лодки вместо сапог 
            2958/17, 
            2959/1</li>
        <li>Сани с сеном 
            <a href="{{ route('text.show', 9297) }}">701/4</a></li>
        <li>Сенной шар 
            <a href="{{ route('text.show', 9297) }}">2949/10</a></li>
        <li>Крутящийся шар 
            2936/3</li>
        <li>Человек, толстый как копна 
            <a href="{{ route('text.show', 9297) }}">3370/3а</a></li>
        <li>Волосатый мужик 
            <a href="{{ route('text.show', 9320) }}">3463/27-35</a></li>
        <li>Мужик (существо) в скрипящих промерзших сапогах 
            <a href="{{ route('text.show', 9301) }}">2397/13</a>, 
            3460/46</li>
        <li>Женщина (synnyn akka) 
            <a href="{{ route('text.show', 9315) }}">2945/7</a></li>
        <li>Северное сияние 
            3460/46 – 3461/1</li>
        <li>Скалки, сыплющиеся сверху 
            <a href="{{ route('text.show', 9303) }}">3429/20</a></li>
    </ol>

    <p><b>C.</b> Локусы, в (на) которых слушают Сюндю</p>
    <ol>
        <li>В доме 
            <a href="{{ route('text.show', 9303) }}">3429/20</a></li>
        <li>Под столом, накрывшись скатертью 
            132/72а</li>
        <li>На печке, свесив голову 
            <a href="{{ route('text.show', 9308) }}">3363/17</a></li>
        <li>Во сне 
            132/91а, 
            <a href="{{ route('text.show', 9298) }}">1734/8</a></li>
        <li>Под окном 
            <a href="{{ route('text.show', 9297) }}">701/4</a>, 
            <a href="{{ route('text.show', 9273) }}">2942/4</a>, 
            <a href="{{ route('text.show', 9290) }}">3450/2</a></li>
        <li>На улице 
            3460/46 – 3461/1</li>
        <li>На чистом месте (поле) 
            <a href="{{ route('text.show', 9287) }}">3370/3</a></li>
        <li>На поле 
            <a href="{{ route('text.show', 9287) }}">3370/3</a></li>
        <li>У проруби 
            <a href="{{ route('text.show', 9297) }}">701/4</a>, 
            <a href="{{ route('text.show', 9273) }}">2942/4</a>, 
            <a href="{{ route('text.show', 9315) }}">2945/7</a>,                      
            <a href="{{ route('text.show', 9311) }}">3367/6</a>, 
            <a href="{{ route('text.show', 9297) }}">3370/3а</a>, 
            3459/7</li>
        <li>На перекрестке трех дорог 
            <a href="{{ route('text.show', 9305) }}">2949/3</a>, 
            <a href="{{ route('text.show', 9265) }}">3064/14</a>, 
            <a href="{{ route('text.show', 9270) }}">3066/23-24</a>, 
            <a href="{{ route('text.show', 9319) }}">3067/27-30</a>, 
            <a href="{{ route('text.show', 9283) }}">3265/58-60</a>, 
            <a href="{{ route('text.show', 9304) }}">3363/15</a>, 
            <a href="{{ route('text.show', 9306) }}">3367/3</a>, 
            <a href="{{ route('text.show', 9317) }}">3432/49а</a>, 
            <a href="{{ route('text.show', 9289) }}">3433/52-53</a></li>
        <li>У бани 
            <a href="{{ route('text.show', 9297) }}">701/4</a>, 
            <a href="{{ route('text.show', 9309) }}">3065/18</a>, 
            3362/5, 
            <a href="{{ route('text.show', 9306) }}">3367/3</a></li>
        <li>У риги 
            <a href="{{ route('text.show', 9297) }}">701/4</a>, 
            <a href="{{ route('text.show', 9318) }}">2249/12</a>, 
            <a href="{{ route('text.show', 9309) }}">3065/18</a>, 
            <a href="{{ route('text.show', 9270) }}">3066/23-24</a></li>
        <li>На сеновале 
            2397/17, 
            <a href="{{ route('text.show', 9283) }}">3265/58-60</a></li>
        <li>На куче мусора с хомутом на шее 
            <a href="{{ route('text.show', 9285) }}">3361/16</a></li>
        <li>На мельничных отходах 
            <a href="{{ route('text.show', 9307) }}">3458/17</a></li>
        <li>У церковной двери 
            <a href="{{ route('text.show', 9288) }}">3431/32</a></li>
    </ol>

    <p><b>D.</b> Действия Сюндю</p>
    <ol>
        <li>Лежит у проруби 
            <a href="{{ route('text.show', 9273) }}">2942/4</a>, 
            2958/17, 
            <a href="{{ route('text.show', 9305) }}">2949/3</a>, 
            <a href="{{ route('text.show', 9297) }}">3370/3а</a>, 
            <a href="{{ route('text.show', 9320) }}">3463/27-35</a></li>
        <li>Выходит из проруби 
            <a href="{{ route('text.show', 9299) }}">1735/6</a></li>
        <li>Выходит из проруби и гонится следом 
            132/91а, 
            <a href="{{ route('text.show', 9297) }}">701/4</a>, 
            <a href="{{ route('text.show', 9298) }}">1734/8</a>, 
            2936/3, 
            2936/7, 
            <a href="{{ route('text.show', 9304) }}">3363/15</a>, 
            <a href="{{ route('text.show', 9297) }}">3370/3а</a></li>
        <li>Опускается с неба 
            132/71а, 
            1531/1, 
            <a href="{{ route('text.show', 9265) }}">3064/14</a>, 
            3066/2-3, 
            <a href="{{ route('text.show', 9270) }}">3066/23-24</a>, 
            <a href="{{ route('text.show', 9283) }}">3265/58-60</a>, 
            3317/19, 
            <a href="{{ route('text.show', 9287) }}">3370/3</a>, 
            <a href="{{ route('text.show', 9288) }}">3431/32</a>, 
            <a href="{{ route('text.show', 9271) }}">3459/17</a>, 
            3464/7, 
            3464/47</li>
        <li>Таскает на коровьей шкуре 
            <a href="{{ route('text.show', 9319) }}">3067/27-30</a></li>
        <li>Убивает парня в риге 
            <a href="{{ route('text.show', 9318) }}">2249/12</a></li>
        <li>Плетью стегает стены сарая 
            2357/13</li>
        <li>Дает волосатую руку из проруби (к богатству) 
            <a href="{{ route('text.show', 9273) }}">2942/4</a></li>
        <li>Освобождает от болезни 
            <a href="{{ route('text.show', 9305) }}">2949/3</a></li>
        <li>Приносит шашку 
            132/72а</li>
        <li>Жалеет хромоножку 
            <a href="{{ route('text.show', 9297) }}">3370/3а</a></li>
        <li>Дает ключи счастья (удачи) 
            2936/3, 
            <a href="{{ route('text.show', 9315) }}">2945/7</a></li>
        <li>Показывает вещие сны 
            <a href="{{ route('text.show', 9283) }}">3265/58-60</a></li>
    </ol>

    <p><b>E.</b> Предсказания Сюндю</p>
    <ol>
        <li>Свадьба 
            132/72а, 
            <a href="{{ route('text.show', 9297) }}">701/4</a>,  
            <a href="{{ route('text.show', 9265) }}">3064/14</a>, 
            <a href="{{ route('text.show', 9319) }}">3067/27-30</a>, 
            <a href="{{ route('text.show', 9283) }}">3265/58-60</a>, 
            <a href="{{ route('text.show', 9285) }}">3361/16</a>, 
            <a href="{{ route('text.show', 9304) }}">3363/15</a>, 
            <a href="{{ route('text.show', 9287) }}">3370/3</a>, 
            <a href="{{ route('text.show', 9289) }}">3433/52-53</a>, 
            <a href="{{ route('text.show', 9271) }}">3459/17</a>, 
            <a href="{{ route('text.show', 9320) }}">3463/27-35</a>, 
            3464/7</li>
        <li>Образование колхозов 
            701/5</li>
        <li>Хороший урожай 
            2650/6</li>
        <li>Строительство железной дороги 
            <a href="{{ route('text.show', 9283) }}">3265/58-60</a></li>
        <li>Тюрьма 
            2650/6</li>
        <li>Война 
            3464/47</li>
    </ol>

    <p><b>F.</b> Действия человека перед встречей и проводами Сюндю</p>
    <ol>
        <li>Перед встречей жарят блины (портянки для Сюндю) 
            2253/10, 
            <a href="{{ route('text.show', 9265) }}">3064/14</a>, 
            3066/2-3, 
            <a href="{{ route('text.show', 9319) }}">3067/27-30</a>, 
            <a href="{{ route('text.show', 9283) }}">3265/58-60</a>, 
            <a href="{{ route('text.show', 9290) }}">3450/2</a>, 
            <a href="{{ route('text.show', 9271) }}">3459/17</a>, 
            <a href="{{ route('text.show', 9320) }}">3463/27-35</a>, 
            3464/7, 
            <a href="{{ route('text.show', 9292) }}">3464/31</a></li>
        <li>Пекут хлебец Сюндю 
            <a href="{{ route('text.show', 9290) }}">3450/2</a>, 
            <a href="{{ route('text.show', 9271) }}">3459/17</a>, 
            <a href="{{ route('text.show', 9292) }}">3464/31</a>, 
            <a href="{{ route('text.show', 9283) }}">3265/58-60</a></li>
        <li>Пекут лепешки 
            3460/46, 
            3461/1</li>
        <li>Перед проводами пекут треугольные пирожки (носки для Сюндю) 
            <a href="{{ route('text.show', 9319) }}">3067/27-30</a></li>
        <li>На проводы выпекают из теста лестницу 
            <a href="{{ route('text.show', 9270) }}">3066/23-24</a></li>
    </ol>

    <p><b>G.</b> Действия человека, когда идешь слушать Сюндю</p>
    <ol>
        <li>Обвести круг иконой, помолиться 
            2656/6</li>
        <li>Обвести круг сковородником 
            2936/3, 
            <a href="{{ route('text.show', 9315) }}">2945/7</a>, 
            3361/6, 
            <a href="{{ route('text.show', 9304) }}">3363/15</a>, 
            <a href="{{ route('text.show', 9306) }}">3367/3</a>, 
            <a href="{{ route('text.show', 9297) }}">3370/3а</a>, 
            <a href="{{ route('text.show', 9317) }}">3432/49а</a>, 
            <a href="{{ route('text.show', 9271) }}">3459/17</a></li>
        <li>Надеть капюшон 
            <a href="{{ route('text.show', 9305) }}">2949/3</a></li>
        <li>Накрыться (скатертью, холстиной) 
            <a href="{{ route('text.show', 9285) }}">3361/16</a>, 
            <a href="{{ route('text.show', 9304) }}">3363/15</a>, 
            <a href="{{ route('text.show', 9297) }}">3370/3а</a>, 
            <a href="{{ route('text.show', 9317) }}">3432/49а</a>, 
            <a href="{{ route('text.show', 9290) }}">3450/2</a></li>
        <li>Надеть хомут на шею 
            <a href="{{ route('text.show', 9285) }}">3361/16</a>, 
            <a href="{{ route('text.show', 9289) }}">3433/52-53</a></li>
        <li>В этот день не шуметь, не смеяться, не баловаться 
            <a href="{{ route('text.show', 9304) }}">3363/15</a>, 
            3429/50, 
            <a href="{{ route('text.show', 9288) }}">3431/32</a>, 
            <a href="{{ route('text.show', 9307) }}">3458/17</a>, 
            <a href="{{ route('text.show', 9271) }}">3459/17</a>, 
            3464/7</li>
    </ol>

    <p><b>H.</b> Действия человека, когда Сюндю погонится следом</p>
    <ol>
        <li>Не смотреть назад 
            <a href="{{ route('text.show', 9298) }}">1734/8</a></li>
        <li>Бросить назад берестяной клубок 
            2936/3, 
            <a href="{{ route('text.show', 9297) }}">2949/10</a></li>
        <li>Воткнуть горящие свечи 
            <a href="{{ route('text.show', 9297) }}">2949/10</a></li>
        <li>С молитвой закрыть двери 
            132/91а, 
            <a href="{{ route('text.show', 9297) }}">701/4</a>, 
            <a href="{{ route('text.show', 9298) }}">1734/8</a>, 
            <a href="{{ route('text.show', 9303) }}">3429/20</a></li>
        <li>Надеть молочные крынки на голову 
            132/91а, 
            <a href="{{ route('text.show', 9288) }}">3431/32</a></li>
    </ol>

    <p><b>I.</b> Запреты (табу) для людей во время святок</p>
    <ol>
        <li>Стирать 
            <a href="{{ route('text.show', 9295) }}">2939/15</a>, 
            2958/17-2959/1, 
            <a href="{{ route('text.show', 9285) }}">3361/16</a>, 
            <a href="{{ route('text.show', 9296) }}">3363/18</a>, 
            <a href="{{ route('text.show', 9286) }}">3367/2</a>, 
            <a href="{{ route('text.show', 9287) }}">3370/3</a>, 
            <a href="{{ route('text.show', 9288) }}">3431/32</a>, 
            <a href="{{ route('text.show', 9289) }}">3433/52-53</a>, 
            <a href="{{ route('text.show', 9307) }}">3458/17</a>, 
            <a href="{{ route('text.show', 9320) }}">3463/27-35</a>, 
            3464/7, 
            <a href="{{ route('text.show', 9292) }}">3464/31</a></li>
        <li>Мыть полы 
            <a href="{{ route('text.show', 9295) }}">2939/15</a>, 
            <a href="{{ route('text.show', 9288) }}">3431/32</a>, 
            <a href="{{ route('text.show', 9271) }}">3459/17</a>, 
            <a href="{{ route('text.show', 9320) }}">3463/27-35</a>, 
            3464/7</li>
        <li>Выливать грязную воду на улицу 
            <a href="{{ route('text.show', 9295) }}">2939/15</a>, 
            <a href="{{ route('text.show', 9319) }}">3067/27-30</a></li>
        <li>Выносить на улицу мусор, золу 
            <a href="{{ route('text.show', 9319) }}">3067/27-30</a>, 
            <a href="{{ route('text.show', 9287) }}">3370/3</a></li>
        <li>Стричь овец 
            <a href="{{ route('text.show', 9286) }}">3367/2</a></li>
        <li>Чесать шерсть 
            <a href="{{ route('text.show', 9286) }}">3367/2</a>, 
            <a href="{{ route('text.show', 9287) }}">3370/3</a>, 
            <a href="{{ route('text.show', 9292) }}">3464/31</a></li>
        <li>Спорить, ругаться 
            <a href="{{ route('text.show', 9294) }}">3265/74-76</a></li>
        <li>Плясать <a href="{{ route('text.show', 9263) }}">1513/1</a></li>
    </ol>

<h4>II. Крещенская баба (Vierissän akka)</h4>
    <p><b>A.</b> Крещенская баба – это</p>
    <ol>
        <li>Водяной (хозяйка воды) 
            <a href="{{ route('text.show', 9257) }}">1547/3</a>, 
            1701/1, 
            <a href="{{ route('text.show', 9239) }}">2213/11</a>, 
            2511/13-14, 
            <a href="{{ route('text.show', 9236) }}">2547/1</a>, 
            2547/20, 
            2610/2, 
            2731/3, 
            <a href="{{ route('text.show', 9238) }}">2929/9</a>, 
            3055/6-9, 
            <a href="{{ route('text.show', 9260) }}">3476/15-16</a></li>
        <li>Бес 
            1595/4, 
            <a href="{{ route('text.show', 9242) }}">1702/17</a>, 
            <a href="{{ route('text.show', 9239) }}">2213/11</a></li>
        <li>Враждебное лесное существо, враг 
            <a href="{{ route('text.show', 9235) }}">2219/25</a></li> 
    </ol>
    
    <p><b>B.</b> Действия Крещенской бабы</p>
    <ol>
        <li>Выходит из проруби 
            <a href="{{ route('text.show', 9263) }}">1547/3</a>, 
            1595/4, 
            <a href="{{ route('text.show', 9237) }}">1596/8</a>, 
            <a href="{{ route('text.show', 9239) }}">2213/11</a>, 
            2547/20, 
            2731/3, 
            <a href="{{ route('text.show', 9239) }}">2927/15</a>, 
            <a href="{{ route('text.show', 9238) }}">2929/9</a>, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a>, 
            3227/17-18</li>
        <li>Гонится за слушающими 
            <a href="{{ route('text.show', 9263) }}">1547/3</a>, 
            1701/1, 
            2219/23, 
            2511/13-14, 
            2547/20, 
            <a href="{{ route('text.show', 9243) }}">2606/20</a>, 
            3055/6-9, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
        <li>Приходит с собачкой 
            <a href="{{ route('text.show', 9253) }}">3350/11</a></li>
        <li>Спускается с неба 
            <a href="{{ route('text.show', 9259) }}">2649/14</a></li>
        <li>Поднимается на небо 
             <a href="{{ route('text.show', 9260) }}">3476/15-16</a></li>
        <li>Поднимается из проруби на крест 
            <a href="{{ route('text.show', 9253) }}">3350/11</a>, 
             <a href="{{ route('text.show', 9260) }}">3476/15-16</a></li>
        <li>Может задать вопросы-загадки 
            1701/1, 
            2219/23, 
            2508/3, 
            <a href="{{ route('text.show', 9243) }}">2606/20</a>, 
            <a href="{{ route('text.show', 9246) }}">2648/17</a>, 
            2731/3, 
            3055/5-9, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a>, 
            3227/17-18, 
            <a href="{{ route('text.show', 9253) }}">3350/11</a></li>
        <li>Тащит за хвост шкуру, на которой сидят слушающие 
            1595/4, 
            <a href="{{ route('text.show', 9242) }}">1702/17</a>, 
            2547/20, 
            3227/17-18</li>
        <li>Бросает бревно 
            <a href="{{ route('text.show', 9235) }}">2219/25</a></li>
        <li>Топит людей, мстя за своего ребенка 
            2511/13-14</li>
        <li>Пинает человека холодным сапогом 
            <a href="{{ route('text.show', 9246) }}">2648/17</a>, 
            <a href="{{ route('text.show', 9247) }}">2648/25</a></li>
        <li>Стрижет овец 
            2610/32</li>
        <li>Рвет нить во время прядения 
            <a href="{{ route('text.show', 9262) }}">1312/1</a></li>
        <li>Дает ключи счастья (удачи) 
            <a href="{{ route('text.show', 9242) }}">1702/17</a>, 
            <a href="{{ route('text.show', 9243) }}">2606/20</a></li>
        <li>Плавает в озере, как медведь 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
        <li>Весной сидит на берегу, толкает лодку 
            2511/13-14</li>
        <li>Летом попадает в тоню 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
    </ol>

    <p><b>C.</b> Предсказания Крещенской бабы</p>
    <ol>
        <li>Вся жизнь 
            1701/1, 
            3055/6-9</li>
        <li>Какой будет год 
            3227/17-18</li>
        <li>Свадьба 
            2219/23, 
            <a href="{{ route('text.show', 9253) }}">3350/11</a></li>
        <li>Смерть 
            <a href="{{ route('text.show', 9263) }}">1547/3</a>, 
            2219/23, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
    </ol>

    <p><b>D.</b> Локусы, на которых человек встречает Крещенскую бабу</p>
    <ol>
        <li>У проруби 
            <a href="{{ route('text.show', 9263) }}">1547/3</a>, 
            1595/4, 
            1696/8, 
            1701/1, 
            <a href="{{ route('text.show', 9239) }}">2213/11</a>, 
            2219/23, 
            2511/13-14, 
            2547/20, 
            <a href="{{ route('text.show', 9243) }}">2606/20</a>, 
            2731/3, 
            <a href="{{ route('text.show', 9239) }}">2927/15</a>, 
            3227/17-18, 
            <a href="{{ route('text.show', 9253) }}">3350/11</a></li>
        <li>На перекрестке 
            <a href="{{ route('text.show', 9239) }}">2927/15</a>, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a>, 
            3227/17-18, 
             <a href="{{ route('text.show', 9260) }}">3476/15-16</a></li>
        <li>У амбара 
            <a href="{{ route('text.show', 9263) }}">1547/3</a>, 
            2511/13-14, 
            <a href="{{ route('text.show', 9239) }}">2927/15</a></li>
        <li>Под окном риги, хлева, бани 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
        <li>У калитки 
            3227/17-18</li>
        <li>На задворках на куче мусора 
            <a href="{{ route('text.show', 9263) }}">1547/3</a>, 
            1595/4</li>
        <li>У рога коровы 
            2511/13-14</li>
    </ol>

    <p><b>E.</b> Действия человека, который пошел слушать предсказания</p>
    <ol>
        <li>Собирает нечетное количество слушающих 
            2219/23, 
            2610/2, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
        <li>Обводит круг вокруг слушающих 
            1595/4, 
            1598/8, 
            <a href="{{ route('text.show', 9242) }}">1702/17</a>, 
            2219/23, 
            2511/13-14, 
            2547/20, 
            <a href="{{ route('text.show', 9247) }}">2648/25</a>, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a>, 
            3227/17-18, 
             <a href="{{ route('text.show', 9260) }}">3476/15-16</a></li>
        <li>Берет с собой икону 
            1595/4, 
            1701/1, 
            <a href="{{ route('text.show', 9242) }}">1702/17</a>, 
            2219/23, 
            <a href="{{ route('text.show', 9247) }}">2648/25</a></li>
        <li>Поворачивается трижды по солнцу 
            1701/1</li>
        <li>Садится на коровью шкуру 
            1595/4, 
            <a href="{{ route('text.show', 9242) }}">1702/17</a>, 
            <a href="{{ route('text.show', 9236) }}">2547/1</a>, 
            2547/20, 
            3227/17-18</li>
        <li>Садится на сани 
            1701/1, 
            1595/4, 
            <a href="{{ route('text.show', 9243) }}">2606/20</a>, 
            3055/6-9, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
        <li>Накрывает всех с головой 
            1595/4, 
            1701/1, 
            2219/23, 
            <a href="{{ route('text.show', 9236) }}">2547/1</a>, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a>, 
            <a href="{{ route('text.show', 9253) }}">3350/11</a></li>
        <li>Просит не шевелиться во время слушанья 
            <a href="{{ route('text.show', 9239) }}">2213/11</a></li>
        <li>Запоминает первое слово, чтобы сказать его последним 
            1595/4</li>
        <li>Заучивает ответы на вопросы Крещенской бабы 
            1701/1, 
            2219/23, 
            2508/3, 
            <a href="{{ route('text.show', 9243) }}">2606/20</a>, 
            <a href="{{ route('text.show', 9246) }}">2648/17</a>, 
            2731/3, 
            3055/5-9, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a>, 
            3227/17-18, 
            <a href="{{ route('text.show', 9253) }}">3350/11</a></li>
        <li>Крещенской бабой пугают детей 
            <a href="{{ route('text.show', 9244) }}">2926/13</a>, 
            <a href="{{ route('text.show', 9239) }}">2927/15</a>, 
            <a href="{{ route('text.show', 9261) }}">3485/16</a></li>
    </ol>

    <p><b>F.</b> Действия человека, когда Крещенская баба гонится следом</p>
    <ol>
        <li>Надевает каждому крынки на голову 
            <a href="{{ route('text.show', 9263) }}">1547/3</a>, 
            1701/1, 
            2219/23, 
            2511/13-14, 
            <a href="{{ route('text.show', 9243) }}">2606/20</a>, 
            3055/6-9, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
        <li>Запрещает оглядываться назад 
            1595/4</li>
    </ol>

    <p><b>G.</b> Запреты (табу) во время Святок (Крещенского промежутка Vierissän keski)</p>
    <ol>
        <li>Выливать грязную воду на улицу 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
        <li>Мыть полы 
            3227/17-18</li>
        <li>Стирать белье 
            <a href="{{ route('text.show', 9262) }}">1312/1</a>, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a>, 
            3227/17-18</li>
        <li>Стричь овец 
            <a href="{{ route('text.show', 9239) }}">2213/11</a>, 
            2610/32, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a>, 
            3227/17-18</li>
        <li>Чесать шерсть 
            <a href="{{ route('text.show', 9262) }}">1312/1</a>, 
            <a href="{{ route('text.show', 9239) }}">2213/11</a>, 
            2610/32, 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
    </ol>

    <p><b>H.</b> Временные периоды, кроме святочного, когда видят Крещенскую бабу</p>
    <ol>
        <li>Летом 
            <a href="{{ route('text.show', 9254) }}">3055/16-19</a></li>
        <li>Весной 
            2511/13-14</li>
    </ol>

<h4>III. Крещенская свинья (Vierissän siga)</h4>
    <p><b>A.</b> Локусы, в которых появляется крещенская свинья</p>
    <ol>
        <li>Появляется из лесу и может утащить туда человека, нарушившего запреты 
            3485/13</li>
    </ol>

    <p><b>B.</b> Действия</p>
    <ol>
        <li>Уносит в лес человека, нарушившего запреты 
            3485/13</li>
    </ol>

<h4>IV. Кегри</h4>
    <p><b>A.</b> Кегри – это</p>
    <ol>
        <li>Невидимый(ая) покровитель(ница) прядения 
            2553/11</li>
    </ol>

    <p><b>B.</b> Действия Кегри</p>
    <ol>
        <li>Может ударить по рукам 
            2553/11</li>
        <li>Уронить колокольчики с шеи животных 
            <a href="{{ route('text.show', 9205) }}">2328/23</a></li>
    </ol>

    <p><b>C.</b> Действия человека перед праздником Кегри</p>
    <ol>
        <li>Снимает колокольчики с шеи животных 
            <a href="{{ route('text.show', 9205) }}">2328/23</a></li>
        <li>Готовит (прядет) моток (клубок) ниток 
            2553/11</li>
    </ol>
@stop



