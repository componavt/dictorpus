                <ul class="nav navbar-nav">
                    <li class="dropdown" id='menu1'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                           {{ trans('navigation.about_project') }} <span class="caret"></span>
                        </a>
                        
                        <ul class="dropdown-menu" role="menu" id='menu1-sub'>
                        @include('header._menu_project')
                        </ul>
                    </li>
                    
                    <li class="dropdown" id='menu2'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ trans('navigation.corpus') }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu" id='menu2-sub'>
                        @include('header._menu_corpus')
                        </ul>
                    </li>
                    <li class="dropdown" id='menu3'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ trans('navigation.dictionary') }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu" id='menu3-sub'>
                        @include('header._menu_dict')
                        </ul>
                    </li>
                    
                    <li class="dropdown" id='menu4'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ trans('navigation.references') }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu" id='menu4-sub'>
                        @include('header._menu_ref')
                        </ul>
                    </li>
                    <!--li id='menu5' class="for-wide-menu">
                        <a href="{{ LaravelLocalization::localizeURL('/corpus/text') }}" class="dropdown-toggle" role="button" aria-expanded="false">
                            {{ trans('navigation.texts') }} 
                        </a>
                    </li>
                    <li id='menu5' class="for-wide-menu">
                        <a href="{{ LaravelLocalization::localizeURL('/corpus/collection') }}" class="dropdown-toggle" role="button" aria-expanded="false">
                            {{ trans('navigation.collections') }} 
                        </a>
                    </li>
                    <li id='menu5' class="for-wide-menu">
                        <a href="{{ LaravelLocalization::localizeURL('/corpus/audiotext/map') }}" class="dropdown-toggle" role="button" aria-expanded="false">
                            {{ trans('navigation.audio_map') }} 
                        </a>
                    </li-->
                </ul>
