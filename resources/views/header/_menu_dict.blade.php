<li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}">{{ trans('navigation.lemmas') }} ({{mb_strtolower(trans('navigation.dictionary'))}})</a></li>
<li><a href="{{ LaravelLocalization::localizeURL('/dict/wordform') }}">{{ trans('navigation.wordforms') }}</a></li>
<li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/relation') }}">{{ trans('navigation.relations') }}</a></li>
<li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/phrases') }}">{{ trans('navigation.phrases') }}</a></li>
<li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_new_list/') }}">{{ trans('dict.new_lemmas') }}</a></li>
<li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_updated_list/') }}">{{ trans('dict.last_updated_lemmas') }}</a></li>
<li><a href="{{ LaravelLocalization::localizeURL('/dict/reverse_lemma') }}">{{ trans('navigation.reverse_dictionary') }}</a></li>
