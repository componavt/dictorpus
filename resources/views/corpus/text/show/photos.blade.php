<div class='photos-b'>
@foreach($photos as $photo)
<img src="{{$text->photoDir().$photo}}">
@endforeach
</div>