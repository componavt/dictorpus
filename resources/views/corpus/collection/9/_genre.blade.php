    <h{{$header}}>{{$genre->name_pl}} ({{$genre->collectionTexts($collection_id)->count()}})</h{{$header}}>
    <ul>
        @foreach ($genre->plots as $plot)
            @if ($plot->texts->count())
        <li><a href="{{ LaravelLocalization::localizeURL('/corpus/collection/'.$collection_id.'/'.$plot->id.'?for_print='.$for_print)}}">{{$plot->name}}</a> ({{$plot->texts->count()}})</li>
            @endif
        @endforeach
    </ul>
