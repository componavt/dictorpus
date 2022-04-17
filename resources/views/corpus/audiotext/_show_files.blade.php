@foreach ($audiotexts as $audiotext)
    <div style='display:flex; margin-bottom: 20px'>
        @include('widgets.audio', ['route'=>$audiotext->url()])
        <div style="padding-left: 10px">
            <i class="fa fa-trash fa-lg" style="color: #972d1a; cursor: pointer" onClick="removeAudio({{$audiotext->text->id}}, {{$audiotext->id}})"></i>
        </div>
    </div>
@endforeach
