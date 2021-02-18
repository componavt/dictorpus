        <h2>{{trans('dict.new_lemmas')}}</h2>
        @foreach ($new_lemmas as $lemma)
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma')}}/{{$lemma->id}}">
                <span>{{$lemma->lemma}}</span></a> 
            <span class="datetime">
                (@if (isset($lemma->user)){{$lemma->user}},@endif
                {{$lemma->created_at->format('d.m.Y, H:i')}})
            </span>
        </p> 
        @endforeach
        <p class="full-list">
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/full_new_list/')}}">{{trans('main.see_full_list')}}</a>
        </p>
