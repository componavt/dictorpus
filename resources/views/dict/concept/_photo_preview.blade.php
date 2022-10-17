<?php 
    $photo = $concept->photoPreview();
?>
@if ($photo) 
    <a href="{{$photo['url']}}" target="_blank"><img src="{{$photo['source']}}"></a>
@endif