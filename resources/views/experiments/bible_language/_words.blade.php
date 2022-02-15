    <h3>{{$h3}}</h3>
    <ol>
    @foreach($words as $word)
        <li>{!! $word->getClearSentence(true) !!}</li>
    @endforeach
    </ol>
