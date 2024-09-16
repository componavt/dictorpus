<?php $locale = LaravelLocalization::getCurrentLocale(); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
@include('header.head_print')
</head>
<body>
    <!--[if lt IE 7]>
    <p class="browsehappy">Вы используете  <strong>слишком старый</strong> браузер. Пожалуйста <a href="http://browsehappy.com/">обновите ваш браузер</a> для нормального серфинга по современным сайтам.</p>
    <![endif]-->
    <div class="container">
        <section>
@include('errors.errmsg')
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1>@yield('page_title')</h1>

                    @yield('body')
                </div>
            </div>
        </section>
    </div>
@include('footer.footer_print')
@include('footer.foot_script')
</body>
</html>
