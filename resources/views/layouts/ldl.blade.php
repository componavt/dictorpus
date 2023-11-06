<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
@include('header.head_ldl')
</head>
<body>
    <!--[if lt IE 7]>
    <p class="browsehappy">Вы используете  <strong>слишком старый</strong> браузер. Пожалуйста <a href="http://browsehappy.com/">обновите ваш браузер</a> для нормального серфинга по современным сайтам.</p>
    <![endif]-->
    <div class="container">
@include('header.header_ldl')
        <section>
@include('errors.errmsg')
            @hasSection('clear_b')
                @yield('clear_b')
            @endif
            @hasSection('body')
            <div class="panel panel-default">
                <div class="panel-body">
                @yield('body')
                </div>
            </div>
            @endif
        </section>
    </div>
@include('footer.footer_ldl')
@include('footer.foot_script')
</body>
</html>
