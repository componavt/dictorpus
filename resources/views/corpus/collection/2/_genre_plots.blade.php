    <h{{$header}}>{{$genre->name_pl}} ({{$genre->collectionTexts(2)->count()}})</h{{$header}}>
    <ul>
        @foreach ($genre->plots as $plot)
        <li>{{$plot->name}} 
            @if ($plot->texts->count())
            (<a href="{{ LaravelLocalization::localizeURL('/corpus/collection/2/'.$plot->id)}}">{{$plot->texts->count()}}</a>)
            @endif
        </li>
        <ul>
            @foreach($plot->topics as $topic)
            <li>{{ $topic->name }} 
                @if ($topic->textsForPlot($plot->id)->count())
                (<a href="{{ LaravelLocalization::localizeURL('/corpus/collection/2/topics/'.$topic->id.'?plot_id='.$plot->id)}}">{{
                    $topic->textsForPlot($plot->id)->count()}}</a>)
                @endif
            </li>            
            @endforeach
        </ul>
        @endforeach
    </ul>
