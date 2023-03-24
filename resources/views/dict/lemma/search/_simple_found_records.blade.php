        <p>{!!trans_choice('search.found_lemmas', found_rem($lemma_total), ['count'=>$count])!!}</p>
        
        <p>{{ trans('search.other_search') }}:</p>
        <ul>
            <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}">{{ trans('search.advanced_search') }} {{ trans('dict.of_lemmas') }}</a></li>
            <li><a href="{{ route('lemma.by_wordforms') }}">{{ trans('dict.search_lemmas_by_wordforms') }}</a></li>
        </ul>
