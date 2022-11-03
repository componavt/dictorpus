                <div class="bottom-menu">
                    <div>
                        <p><a class="bottom-menu-title" href="{{ LaravelLocalization::localizeURL('/') }}">{{ trans('navigation.about_project') }}</a></p>
                        @include('header._menu_project')
                    </div>
                    
                    <div>
                        <p class="bottom-menu-title">{{ trans('navigation.corpus') }}</p>
                        @include('header._menu_corpus')
                    </div>
                    
                    <div>
                        <p class="bottom-menu-title">{{ trans('navigation.dictionary') }}</p>
                        @include('header._menu_dict')
                    </div>
                    
                    <div>
                        <p class="bottom-menu-title">{{ trans('navigation.references') }}</p>
                        @include('header._menu_ref')
                    </div>
                </div>
