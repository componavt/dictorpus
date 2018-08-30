        <h2>{{trans('corpus.last_updated_texts')}}</h2>
        @foreach ($last_updated_texts as $text)
        <p><a href="{{ LaravelLocalization::localizeURL('corpus/text')}}/{{$text->id}}">
                <span>{{$text->title}}</span></a> 
            <span class="datetime">
                (@if (isset($text->user)){{$text->user}},@endif
                {{$text->updated_at->format('d.m.Y, H:i')}})
            </span>
        </p> 
        @endforeach
        <p class="full-list">
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_updated_list/')}}">{{trans('main.see_all_texts')}}</a>
        </p>
