<div style="display: flex; justify-content: space-between">
@foreach ($legend as $color => $color_text)
    <div><span class="marker-icon marker-{{$color}}">&nbsp;</span> {{$color_text}}</div>
    @endforeach
</div>    
    <div id="mapid" style="margin-top: 20px; width: 100%; min-width: 750px; height: 2100px;"></div>
