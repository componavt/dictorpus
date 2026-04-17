@if ($video) 
    @if ($video->rutube_id)
        @include('widgets.rutube', ['video' => $video->rutube_id])
    @elseif ($video->youtube_id)
        @include('widgets.youtube', ['video' => $video->youtube_id])
    @endif
@endif