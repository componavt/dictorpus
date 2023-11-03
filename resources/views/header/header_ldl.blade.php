        <header id="header">
            <div class="logo">
                <a href="/">
                    <img src="/images/logo.png">
                </a>
                <div>
                    <a href="/olodict" class='site-title-1'>
                        {{trans('olodict.site_title_1')}}
                    </a>
                    <a href="/olodict" class='site-title-2'>
                        {{trans('olodict.site_title_2')}}
                    </a>
                    <a href="/olodict" class='site-title-2'>
                        {{trans('olodict.site_title_3')}}
                    </a>
                </div>
            </div>
            <div class="menu">
                @include('header.lang_switch', ['with_text'=>true])
                <li><a class='menu-help' href="{{ LaravelLocalization::localizeURL('/olodict/help')}}">{{trans('olodict.help')}}</a></li>
            </div>
        </header>
