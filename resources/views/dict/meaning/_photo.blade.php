<?php 
    $photo = $meaning->concepts[0]->photoInfo();
?>
@if ($photo) 
    <a href="{{$photo['url']}}" target="_blank"><img src="{{$photo['source']}}"></a>
@endif