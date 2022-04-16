@foreach ($audiotexts as $audiotext)
    <div class=''>
        @include('widgets.audio', ['route'=>$audiotext->url()])
    </div>
@endforeach
