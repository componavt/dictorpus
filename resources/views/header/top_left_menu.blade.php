                <ul class="nav navbar-nav">
                    <li><a href="{{ LaravelLocalization::localizeURL('/') }}">{{ trans('navigation.home') }}</a></li>
                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ trans('navigation.dictionary') }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}">{{ trans('navigation.lemmas') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/wordform') }}">{{ trans('navigation.wordforms') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/relation') }}">{{ trans('navigation.relations') }}</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ trans('navigation.references') }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lang') }}">{{ trans('navigation.langs') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/dialect') }}">{{ trans('navigation.dialects') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/pos') }}">{{ trans('navigation.parts_of_speech') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/gramset') }}">{{ trans('navigation.gramsets') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/gram') }}">{{ trans('navigation.grams') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/relation') }}">{{ trans('navigation.relations') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/corpus') }}">{{ trans('navigation.corpuses') }}</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ trans('navigation.corpus') }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/text') }}">{{ trans('navigation.texts') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/informant') }}">{{ trans('navigation.informants') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/place') }}">{{ trans('navigation.places') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/region') }}">{{ trans('navigation.regions') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/district') }}">{{ trans('navigation.districts') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/genre') }}">{{ trans('navigation.genres') }}</a></li>
                        </ul>
                    </li>
                </ul>
