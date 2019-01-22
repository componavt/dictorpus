                <div class="bottom-menu">
                    <div>
                        <p><a class="bottom-menu-title" href="{{ LaravelLocalization::localizeURL('/') }}">{{ trans('navigation.about_project') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/about_veps') }}">{{ trans('navigation.about_veps') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/about_karelians') }}">{{ trans('navigation.about_karelians') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/stats') }}">{{ trans('navigation.stats') }}</a></p>
                        <p><a href="http://dictorpus.krc.karelia.ru/{{$locale}}/dumps">{{ trans('navigation.dumps') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/permission') }}">{{ trans('navigation.permission') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/grants') }}">{{ trans('navigation.grants') }}</a></p>
                    </div>
                    
                    <div>
                        <p class="bottom-menu-title">{{ trans('navigation.dictionary') }}</p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}">{{ trans('navigation.lemmas') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/wordform') }}">{{ trans('navigation.wordforms') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/relation') }}">{{ trans('navigation.relations') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/omonyms') }}">{{ trans('navigation.omonyms') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/phrases') }}">{{ trans('navigation.phrases') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/reverse_lemma') }}">{{ trans('navigation.reverse_dictionary') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_new_list/') }}">{{ trans('dict.new_lemmas') }}</a></p>
                        <!--p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_updated_list/') }}">{{ trans('dict.last_updated_lemmas') }}</a></p-->
                    </div>
                    
                    <div>
                        <p class="bottom-menu-title">{{ trans('navigation.references') }}</p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lang') }}">{{ trans('navigation.langs') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/dialect') }}">{{ trans('navigation.dialects') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/pos') }}">{{ trans('navigation.parts_of_speech') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gramset') }}">{{ trans('dict.gramsets') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/gram') }}">{{ trans('navigation.grams') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/dict/relation') }}">{{ trans('navigation.relations') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/corpus') }}">{{ trans('navigation.corpuses') }}</a></p>
                    </div>

                    <div>
                        <p class="bottom-menu-title">{{ trans('navigation.corpus') }}</p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text') }}">{{ trans('navigation.texts') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/informant') }}">{{ trans('navigation.informants') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/place') }}">{{ trans('navigation.places') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/region') }}">{{ trans('navigation.regions') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/district') }}">{{ trans('navigation.districts') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/genre') }}">{{ trans('navigation.genres') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/recorder') }}">{{ trans('navigation.recorders') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/word/freq_dict') }}">{{ trans('navigation.word_frequency') }}</a></p>
                        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_new_list/') }}">{{ trans('corpus.new_texts') }}</a></p>
                        <!--p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_updated_list/') }}">{{ trans('corpus.last_updated_texts') }}</a></p-->
                    </div>
                </div>
