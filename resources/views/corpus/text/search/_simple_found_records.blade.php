        <p>{!!trans_choice('search.found_texts', found_rem($text_total), ['count'=>$count])!!}.</p>
        
        <p>{{ trans('search.other_search') }}:</p>
        <ul>
            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/text/') }}">{{ trans('search.advanced_search') }} {{ trans('corpus.of_texts') }}</a></li>
            <li><a href="{{ LaravelLocalization::localizeURL('/corpus/sentence') }}">{{ trans('corpus.gram_search') }}</a></li>
        </ul>
