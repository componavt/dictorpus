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
                <ul class="nav navbar-nav">
                    <li><a href="{{ LaravelLocalization::localizeURL('/') }}">{{ trans('navigation.home') }}</a></li>
                    <li><a href="{{ LaravelLocalization::localizeURL('/dict/lang') }}">{{ trans('navigation.langs') }}</a></li>
                    <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}">{{ trans('navigation.lemmas') }}</a></li>
                </ul>

                
                <ul class="nav navbar-nav navbar-right lang">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        @if ($localeCode != LaravelLocalization::getCurrentLocale())
                        <li>
                            <a rel="alternate" hreflang="{{$localeCode}}" href="{{LaravelLocalization::getLocalizedURL($localeCode) }}">
                                {{ $properties['native'] }}
                            </a>
                        </li>
                        @endif
                    @endforeach
                </ul>
                
                
                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    
                    
                    <!-- Authentication Links -->
                        <li class="dropdown">
                    @if ($user=Sentinel::check())
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ $user->first_name }} {{ $user->last_name }}<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                    @else
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Login<span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/login') }}">Login</a></li>
                                <li><a href="{{ url('/register') }}">Register</a></li>
                                <li><a href="{{ url('/reset') }}">Reset</a></li>
                            </ul>
                    @endif
                        </li>
                </ul>
            </div>
        </div>
    </nav>
