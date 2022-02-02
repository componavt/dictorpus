        <h4>{{ trans('corpus.corpus') }}: {{ $text->corpus->name }}</h4>
        
        @if ($text->genresToString())
        <p><b>{{trans('corpus.genre')}}:</b> <i>{{ $text->genresToString() }}</i></p>
        @endif
        
        @if ($text->plotsToString())
        <b>{{trans('corpus.plot')}}:</b> <i>{{ $text->plotsToString() }}</i></p>
        @endif
        
        @if (sizeof($text->topicsToArray()))
        <div class="topic-list">
            <p class="topic-list-title">{{trans('navigation.topics')}}:</p>
            {!! join("<br>\n", $text->topicsToArray()) !!}
        </div>
        @endif
        
        @if ($text->event)
        <p> 
            @include('corpus.event._to_string',['event'=>$text->event, 'lang_id' => $text->lang_id])
        </p>
        @endif
        
        @if ($text->source)
        <p> 
            @include('corpus.source._to_string',['source'=>$text->source])
        </p>
        @endif
      
