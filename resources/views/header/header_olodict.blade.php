        <header id="header">
            <div class="logo">
                <a href="/olodict">
                    <img src="/images/logo.png">
                </a>
                <a href="/olodict">
                    {{trans('olodict.site_title')}}<br>{{trans('olodict.site_title_add')}}
                </a>
            </div>
            <div class="menu">
                <li><a href="{{ LaravelLocalization::localizeURL('/olodict/help')}}">{{trans('olodict.help')}}</a></li>
                @include('header.lang_switch', ['with_text'=>false])
            </div>
        </header>
