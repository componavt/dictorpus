@foreach ($audiotexts as $audiotext)
    <div class=''>
        @include('widgets.audio', ['route'=>route('audiotext.show', ['id'=>$audiotext->id])])
    </div>
@endforeach
