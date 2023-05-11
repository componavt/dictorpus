    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="Russian language, Veps language, Karelian language, corpus linguistics, computer-readable dictionary, русский язык, вепсский язык, карельский язык, корпусная лингвистика, машиночитаемый словарь">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--meta property="og:type" content="website">
    <meta property="og:site_name" content="VepKar">
    <meta property="og:title" content="Open corpus of Veps and Karelian languages">
    <meta property="og:description" content="VepKar — an Open corpus of Veps and Karelian languages containing dictionaries and corpora of the Baltic-Finnish languages of Karelia peoples.">
    <meta property="og:url" content="http://dictorpus.krc.karelia.ru/{{LaravelLocalization::getCurrentLocale()}}">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:image" content="http://dictorpus.krc.karelia.ru/images/logo.en.png">
    <meta property="og:image:width" content="365">
    <meta property="og:image:height" content="130"-->

    <title>{{ trans('main.site_abbr') }} :: @yield('title')</title>
    
    <!-- Fonts -->
    {!!Html::style('css/font-awesome_5.6.3.css')!!}
    <!--link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous"-->

    <!-- Styles -->
    {!!Html::style('css/bootstrap.min.css')!!}    
    {!!Html::style('css/languages.min.css')!!}    
    {!!Html::style('css/main.css')!!}
    
    @yield('headExtra')
    
    
