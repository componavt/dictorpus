    <!-- JavaScripts -->
    {!!Html::script('js/jquery-3.1.0.min.js')!!}
    {!!Html::script('js/bootstrap.min.js')!!}
    {!!Html::script('js/menu.js')!!}    

    @yield('footScriptExtra')

    <script type="text/javascript">
        $(document).ready(function(){
            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
            changeWidthDropDownMenu();
            @yield('jqueryFunc')
        });
    </script>
