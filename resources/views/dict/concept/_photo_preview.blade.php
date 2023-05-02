    @if ($photo['url'])
    <a href="{{$photo['url']}}" target="_blank">
    @endif
        <img src="{{$photo['source']}}">
    @if ($photo['url'])
    </a>
    @endif
