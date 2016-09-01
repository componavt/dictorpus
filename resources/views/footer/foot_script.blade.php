    <!-- JavaScripts -->
    {!!Html::script('js/jquery-3.1.0.min.js')!!}
    {!!Html::script('js/bootstrap.min.js')!!}
    <!--script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script-->
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}

    @yield('footScriptExtra')

    <script type="text/javascript">
        $(document).ready(function(){
            @yield('jqueryFunc')
        });
    </script>
