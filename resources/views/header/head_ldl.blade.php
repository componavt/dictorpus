<?php $locale = LaravelLocalization::getCurrentLocale(); ?>    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="Russian language, Karelian language, corpus linguistics, computer-readable dictionary, русский язык, карельский язык, корпусная лингвистика, машиночитаемый словарь">

    <title> 
    @hasSection('title')
        {{ trans('ldl.site_abbr') }}:: @yield('title')
    @else
        {{ trans('ldl.site_title') }}
    @endif
    </title>
    
    <!-- Fonts -->
    {!!Html::style('css/font-awesome_5.6.3.css')!!}

    <!-- Styles -->
    {!!Html::style('css/bootstrap.min.css')!!}    
    {!!Html::style('css/languages.min.css')!!}    
    {!!Html::style('css/ldl.css')!!}
    
    @yield('headExtra')
    
    
