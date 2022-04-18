                <ul class="nav navbar-nav">
                    <li class="dropdown" id='menu1'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                           {{ trans('navigation.about_project') }} <span class="caret"></span>
                        </a>
                        
                        <ul class="dropdown-menu" role="menu" id='menu1-sub'>
                            <li><a href="{{ LaravelLocalization::localizeURL('/') }}">{{ trans('navigation.home') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/page/participants') }}">{{ trans('navigation.participants') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/page/sources') }}">{{ trans('navigation.sources') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/page/publ') }}">{{ trans('navigation.publications') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/page/about_veps') }}">{{ trans('navigation.about_veps') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/page/about_karelians') }}">{{ trans('navigation.about_karelians') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/stats') }}">{{ trans('navigation.stats') }}</a></li>
                            <li><a href="http://dictorpus.krc.karelia.ru/{{$locale}}/dumps">{{ trans('navigation.dumps') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/page/permission') }}">{{ trans('navigation.permission') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/page/grants') }}">{{ trans('navigation.grants') }}</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown" id='menu2'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ trans('navigation.corpus') }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu" id='menu2-sub'>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/text') }}">{{ trans('navigation.texts') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/speech_corpus') }}">{{ trans('navigation.speech_corpus') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/sentence') }}">{{ trans('corpus.gram_search') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/audiotext/map') }}">{{ trans('navigation.audio_map') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/collection') }}">{{ trans('navigation.collections') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/video') }}">{{ trans('navigation.video') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_new_list/') }}">{{ trans('corpus.new_texts') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_updated_list/') }}">{{ trans('corpus.last_updated_texts') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/page/corpus_frequency') }}">{{ trans('navigation.corpus_freq') }}</a></li>
                        </ul>
                    </li>
                    <li class="dropdown" id='menu3'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ trans('navigation.dictionary') }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu" id='menu3-sub'>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}">{{ trans('navigation.lemmas') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/wordform') }}">{{ trans('navigation.wordforms') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/relation') }}">{{ trans('navigation.relations') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/phrases') }}">{{ trans('navigation.phrases') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_new_list/') }}">{{ trans('dict.new_lemmas') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_updated_list/') }}">{{ trans('dict.last_updated_lemmas') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/reverse_lemma') }}">{{ trans('navigation.reverse_dictionary') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/page/dict_selections') }}">{{ trans('navigation.dict_selections') }}</a></li>
                        </ul>
                    </li>
                    
                    <li class="dropdown" id='menu4'>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ trans('navigation.references') }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu" id='menu4-sub'>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lang') }}">{{ trans('navigation.langs') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/dialect') }}">{{ trans('navigation.dialects') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/corpus') }}">{{ trans('navigation.corpuses') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/genre') }}">{{ trans('navigation.genres') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/plot') }}">{{ trans('navigation.plots') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/topic') }}">{{ trans('navigation.topics') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/place') }}">{{ trans('navigation.places') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/district') }}">{{ trans('navigation.districts') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/region') }}">{{ trans('navigation.regions') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/informant') }}">{{ trans('navigation.informants') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/author') }}">{{ trans('navigation.authors') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/recorder') }}">{{ trans('navigation.recorders') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/pos') }}">{{ trans('navigation.parts_of_speech') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/gramset') }}">{{ trans('navigation.gramsets') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/gram') }}">{{ trans('navigation.grams') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/relation') }}">{{ trans('navigation.relations') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/concept_category') }}">{{ trans('navigation.concept_categories') }}</a></li>
                            <li><a href="{{ LaravelLocalization::localizeURL('/dict/concept') }}">{{ trans('navigation.concepts') }}</a></li>
                        </ul>
                    </li>
                </ul>
