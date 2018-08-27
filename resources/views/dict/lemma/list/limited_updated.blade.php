        <h2>{{trans('dict.last_updated_lemmas')}}</h2>
        <ol>
        @foreach ($last_updated_lemmas as $lemma)
        <li><a href="{{ LaravelLocalization::localizeURL('/dict/lemma')}}/{{$lemma->id}}">{{$lemma->lemma}}</a> 
            (@if (isset($lemma->user)){{$lemma->user}},@endif
            <span class="datetime">{{$lemma->updated_at->formatLocalized(trans('main.datetime_format'))}}</span>)
        </li> 
        @endforeach
        </ol>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_updated_list/')}}">{{trans('main.see_full_list')}}</a></p>
