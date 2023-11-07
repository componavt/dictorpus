    @if ($with_url && $photo['url'])
    <a href="{{$photo['url']}}" target="_blank">
    @endif
        <img src="{{$photo['source']}}">
    @if ($with_url && $photo['url'])
    </a>
    @endif
