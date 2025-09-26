<?php $locale = LaravelLocalization::getCurrentLocale(); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
@include('header._google_analytics')    
@include('header.head')
</head>
<body>
    <!--[if lt IE 7]>
    <p class="browsehappy">Вы используете  <strong>слишком старый</strong> браузер. Пожалуйста <a href="http://browsehappy.com/">обновите ваш браузер</a> для нормального серфинга по современным сайтам.</p>
    <![endif]-->
    <div class="container">
@include('header.header')
@include('header.menu')
        <section>
@include('errors.errmsg')
@yield('content')
    @if(User::checkAccess('corpus.edit') && !empty($scriptTime))
        <p>{{ trans('messages.script_executed', ['n'=>round($scriptTime, 1)])}}</p>
    @endif
        </section>
    </div>
@include('footer.footer')
@include('footer.foot_script')
</body>
</html>
