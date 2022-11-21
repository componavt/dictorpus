<div class='photos-b'>
@foreach($photos as $photo => $photo_big)
    <img class='photo' src="{{$text->photoDir().$photo}}" data-big="{{$text->photoDir().$photo_big}}">
@endforeach
</div>