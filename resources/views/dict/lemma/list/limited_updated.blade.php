        <h2>{{trans('dict.last_updated_lemmas')}}</h2>
        @foreach ($last_updated_lemmas as $lemma)
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma')}}/{{$lemma->id}}">
                <span>{{$lemma->lemma}}</span></a> 
            <span class="datetime">
                (@if (isset($lemma->user)){{$lemma->user}},@endif
                {{$lemma->updated_at->format('d.m.Y, H:i')}})
            </span>
        </p> 
        @endforeach
        <p class="full-list">
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_updated_list/')}}">{{trans('main.see_all_lemmas')}}</a>
        </p>
