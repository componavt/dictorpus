        <h2>{{trans('corpus.new_texts')}}</h2>
        @foreach ($new_texts as $text)
        <p><a href="{{ LaravelLocalization::localizeURL('corpus/text')}}/{{$text->id}}">
                <span>{{$text->title}}</span></a> 
            <span class="datetime">
            (@if (isset($text->user)){{$text->user}},@endif 
            {{$text->created_at->formatLocalized(trans('main.datetime_format'))}})
            </span>
        </p> 
        @endforeach
        <p class="full-list">
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_new_list/')}}">{{trans('main.see_full_list')}}</a>
        </p>

