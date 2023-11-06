        <header id="header">
            <div class="logo">
                <a href="/">
                    <img src="/images/logo.png">
                </a>
                <div>
                    <a href="/ldl" class='site-title-1'>
                        {{trans('ldl.site_title_1')}}
                    </a>
                    <a href="/ldl" class='site-title-2'>
                        {{trans('ldl.site_title_2')}}
                    </a>
                </div>
            </div>
            <div class="menu">
                @include('header.lang_switch', ['with_text'=>true])
            </div>
        </header>
