<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <!--a class="navbar-brand" href="{{ url('/') }}">
                    Laravel
                </a-->
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                @include('header.top_left_menu')
                
                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    @include('header.lang_switch')                    
                    
                    <!-- Authentication Links -->
                    @include('header.auth_links')
                </ul>
            </div>
        </div>
    </nav>
