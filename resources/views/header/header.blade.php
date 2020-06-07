        <header id="header">
            <div class="logo">
                <a href="/"><img src="/images/logo.{{LaravelLocalization::getCurrentLocale()}}.png"></a>
            </div>
            <div class="user-enter">
<!-- Authentication Links -->            
@include('header.auth_links')
            </div>
        </header>
