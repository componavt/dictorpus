@if ($text->title)
        <h4>
        @if ($text->authors)
            {{$text->authorsToString()}}<h4>
        @endif
        <h3>
        {{ $text->title }}
        </h3>
        <h5>
        {{ $text->lang->name }}
        @if ($text->dialectsToString())
        <br>{{$text->dialectsToString()}}
        @endif
        </h5>
@endif      

@if ($text->text)
            <div id="text">{!! highlight($text->textForPage($url_args), $url_args['search_w'], 'search-word'); !!}</div>
@endif      
