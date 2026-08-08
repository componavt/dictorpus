<div class='photos-b'>
    @foreach ($photos as $photo)
    <img class='photo' src="{{ $photo->getUrl('thumb') }}" data-big="{{ $photo->getUrl() }}" data-title="{{ str_replace('"', '\"', $photo->name) }}">
    @endforeach
</div>