        <h2>{{trans('corpus.last_updated_texts')}}</h2>
        <ol>
        @foreach ($last_updated_texts as $text)
        <li><a href="{{ LaravelLocalization::localizeURL('corpus/text')}}/{{$text->id}}">{{$text->title}}</a> 
            (
                @if (isset($text->user))
                    {{$text->user}}, 
                @endif
                <span class="datetime">{{$text->updated_at->formatLocalized(trans('main.datetime_format'))}})</span>
        </li> 
        @endforeach
        </ol>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_updated_list/')}}">{{trans('main.see_full_list')}}</a></p>
