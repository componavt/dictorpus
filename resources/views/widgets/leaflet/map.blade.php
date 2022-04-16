<div style="display: flex; justify-content: space-between">
@foreach ($legend as $color => $color_text)
    <div style="padding-right: 10px"><span class="marker-icon marker-{{$color}}">&nbsp;</span> {{$color_text}}</div>
    @endforeach
</div>    
    <div id="mapid" style="margin-top: 20px; width: 100%; height: 1300px;"></div>
