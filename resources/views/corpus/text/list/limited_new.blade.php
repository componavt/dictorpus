        <h2>{{trans('corpus.new_texts')}}</h2>
        <ol>
        @foreach ($new_texts as $text)
        <li><a href="{{ LaravelLocalization::localizeURL('corpus/text')}}/{{$text->id}}">{{$text->title}}</a> 
            ({{$text->user}}, <span class="datetime">{{$text->created_at->formatLocalized(trans('main.datetime_format'))}})</span></li> 
        @endforeach
        </ol>
        <p><a href="{{ LaravelLocalization::localizeURL('/corpus/text/full_new_list/')}}">{{trans('main.see_full_list')}}</a></p>
