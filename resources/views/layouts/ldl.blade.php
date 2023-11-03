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
@include('header.header_olodict')
        <section>
@include('errors.errmsg')
            <div class="main-panel row">
                <div class="left-column col-sm-3">
                    @yield('left-column')
                </div>
                <div class="right-column col-sm-9">
                    @yield('body')
                </div>
            </div>
        </section>
    </div>
@include('footer.footer_olodict')
@include('footer.foot_script_olodict')
</body>
</html>
