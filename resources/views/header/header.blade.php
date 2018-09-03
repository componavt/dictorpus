        <header id="header" class="row">
            <div class="logo col-md-6">
                <a href="/"><img src="/images/logo.{{LaravelLocalization::getCurrentLocale()}}.png"></a>
            </div>
            <div class="col-md-6 user-enter">
<!-- Authentication Links -->            
@include('header.auth_links')
            </div>
        </header>
