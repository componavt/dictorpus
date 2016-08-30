@extends('layouts.master')

@section('content')
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('navigation.about_project') }}</div>

                <div class="panel-body">
                    
                    <p>Открытый корпус вепсского и карельского языков (кратко "ВепКар")
                       содержит словари и корпуса прибалтийско-финских языков народов Карелии.</p>

                    <p>Корпус карельского языка включает собственно-карельское, ливвиковское и людиковское наречия, 
                        обладающие в настоящее время собственными младописьменными формами.
                        Проект ВепКар является продолжением работ по  
                        <a href="http://vepsian.krc.karelia.ru">Корпусу вепсского языка</a>.
                        Продолжается работа по наполнению словаря и корпуса вепсского языка.
                    </p>
 
                    <p>В проекте являются открытыми программное обеспечение 
                        (система <a href="https://github.com/componavt/dictorpus">Dictorpus</a>) 
                        и данные (лицензия <a href="https://creativecommons.org/licenses/by/4.0/deed.ru">Creative Commons Attribution 4.0 International</a>).

                    <p>Над проектом работают сотрудники <a href="http://www.krc.karelia.ru">Карельского научного центра РАН</a>.</p>
                    
                </div>
            </div>
@endsection
