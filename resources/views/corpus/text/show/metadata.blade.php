<div class="text-metadata">
        <h4>{{ trans('corpus.corpus') }}: {{ $text->corpus->name }}</h4>
        
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
        
        @if ($text->plotsToString())
        <b>{{trans('corpus.plot')}}:</b> <i>{!! $text->plotsToString('/corpus/text?search_plot=') !!}</i></p>
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
</div>      
