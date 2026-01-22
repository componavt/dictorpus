<div class="text-metadata">
        <h4>{{ trans('corpus.corpus') }}: {!! $text->corpusesToString('/corpus/text?search_corpus=') !!}</h4>
        
        @if ($text->genresToString())
        <p><b>{{trans('corpus.genre')}}:</b> <i>{!! $text->genresToString('/corpus/text?search_genre=') !!}</i></p>
        @endif
        
        @if ($text->cyclesToString())
        <b>{{trans('corpus.cycle')}}:</b> <i>{!! $text->cyclesToString() !!}</i></p>
        @endif
        
        @if ($text->motivesToString())
        <div class="topic-list">
            <p class="topic-list-title">{{trans('navigation.motives')}}:</p>
            {!! $text->motivesToString('/corpus/text?search_motive=') !!}
        </div>
        @endif
        
        @if (sizeof($text->plotsToArray())==1)
        <b>{{trans('corpus.plot')}}:</b> <i>{!! $text->plotsToString('/corpus/text?search_plot=') !!}</i></p>
        @elseif (sizeof($text->plotsToArray())>1)
        <div class="topic-list">
            <p class="topic-list-title">{{trans('navigation.plots')}}:</p>
            {!! join("<br>\n", $text->plotsToArray('/corpus/text?search_plot=')) !!}
        </div>
        @endif
        
        @if (sizeof($text->topicsToArray()))
        <div class="topic-list">
            <p class="topic-list-title">{{trans('navigation.topics')}}:</p>
            {!! join("<br>\n", $text->topicsToArray('/corpus/text?search_topic=')) !!}
        </div>
        @endif
        
        @if ($text->getCollectionId())
        <p><b>коллекция:</b> 
        &#171;{!! to_link(trans('collection.name_list')[$text->getCollectionId()], 
                               '/corpus/collection/'.$text->getCollectionId()) !!}&#187;</p>
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
        
        @if ($text->placesToString())
        <p><b>{{trans('corpus.place_mentioned')}}:</b> <i>{!! $text->placesToString('/corpus/text?search_place=') !!}</i></p>
        @endif
        
        @if ($text->comment)
        <p>{{$text->comment}}</p>
        @endif

        @if ($text->toponymUrls())
        <p><b>{{trans('corpus.topkar_toponyms')}}:</b> {!! $text->toponymUrls() !!}</p>
        @endif
</div>      
