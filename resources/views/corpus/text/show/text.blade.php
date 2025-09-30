@if ($text->title)
        <h4>
        @if ($text->authors)
            {{$text->authorsToString()}}<h4>
        @endif
        <h3>
        {!!highlight($text->title, $url_args['search_w'], 'search-word')!!}
        </h3>
        <h5>
        {{ $text->lang->name }}
        @if ($text->dialectsToString())
        <br>{{$text->dialectsToString()}}
        @endif
        </h5>
@endif      

@if ($text->text)
            <div id="text">{!! highlight(highlight($text->textForPage($url_args, 
                                                                      $meanings_by_wid ?? [], 
                                                                      $gramsets_by_wid ?? [],
                                                                      $wordforms ?? []), 
                                                   $url_args['search_w']), 
                                                   $url_args['search_text']); !!}</div>
@endif      
