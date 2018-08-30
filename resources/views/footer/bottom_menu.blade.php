                <div class="bottom-menu">
                    <div>
                        <p><a class="bottom-menu-title" href="{{ LaravelLocalization::localizeURL('/') }}">{{ trans('navigation.about_project') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/about_veps') }}"><span>{{ trans('navigation.about_veps') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/about_karelians') }}"><span>{{ trans('navigation.about_karelians') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/stats') }}"><span>{{ trans('navigation.stats') }}</span></a></p>
                        <p><a href="http://dictorpus.krc.karelia.ru/dumps"><span>{{ trans('navigation.dumps') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/permission') }}"><span>{{ trans('navigation.permission') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/grants') }}"><span>{{ trans('navigation.grants') }}</span></a></p>
                    </div>
                    
                    <div>
                        <p class="bottom-menu-title">{{ trans('navigation.dictionary') }}</p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}"><span>{{ trans('navigation.lemmas') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/wordform') }}"><span>{{ trans('navigation.wordforms') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/relation') }}"><span>{{ trans('navigation.relations') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/omonyms') }}"><span>{{ trans('navigation.omonyms') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/phrases') }}"><span>{{ trans('navigation.phrases') }}</span></a></p>
                    </div>
                    
                    <div>
                        <p class="bottom-menu-title">{{ trans('navigation.references') }}</p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lang') }}"><span>{{ trans('navigation.langs') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/dialect') }}"><span>{{ trans('navigation.dialects') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/pos') }}"><span>{{ trans('navigation.parts_of_speech') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gramset') }}"><span>{{ trans('navigation.gramsets') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gram') }}"><span>{{ trans('navigation.grams') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/relation') }}"><span>{{ trans('navigation.relations') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/corpus') }}"><span>{{ trans('navigation.corpuses') }}</span></a></p>
                    </div>

                    <div>
                        <p class="bottom-menu-title">{{ trans('navigation.corpus') }}</p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text') }}"><span>{{ trans('navigation.texts') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/informant') }}"><span>{{ trans('navigation.informants') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/place') }}"><span>{{ trans('navigation.places') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/region') }}"><span>{{ trans('navigation.regions') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/district') }}"><span>{{ trans('navigation.districts') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/genre') }}"><span>{{ trans('navigation.genres') }}</span></a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/recorder') }}"><span>{{ trans('navigation.recorders') }}</span></a></p>
                    </div>
                </div>
