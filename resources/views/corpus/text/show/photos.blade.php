<div style='margin-bottom: 20px;'>
@foreach($photos as $photo)
<img src="{{$text->photoDir().$photo}}">
@endforeach
</div>