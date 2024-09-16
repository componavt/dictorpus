    <h{{$header}}>{{$genre->name_pl}} ({{$genre->collectionTexts($collection_id)->count()}})</h{{$header}}>
    <ul>
        @foreach ($genre->plots as $plot)
            @if ($plot->texts->count())
        <li><a href="{{ LaravelLocalization::localizeURL('/corpus/collection/2/'.$plot->id.'?for_print='.$for_print)}}">{{$plot->name}}</a> ({{$plot->texts->count()}})</li>
{{--                @foreach ($plot->texts()->whereIn('lang_id', $lang_id)->orderBy('sequence_number')->get() as $text)
    @include('corpus.collection._text', 
            ['event' => $text->event, 'source' => $text->source])
                @endforeach --}}
            @endif
        @endforeach
    </ul>
