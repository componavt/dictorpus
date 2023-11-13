<div class='photos-b'>
{{--@foreach($photos as $photo => $photo_big)
    <img class='photo' src="{{$text->photoDir().$photo}}" data-big="{{$text->photoDir().$photo_big}}">
@endforeach--}}
    @foreach ($photos as $photo)
    <img class='photo' src="{{ $photo->getUrl('thumb') }}" data-big="{{ $photo->getUrl() }}" data-title="{{ str_replace('"', '\"', $photo->name) }}">
    @endforeach
</div>