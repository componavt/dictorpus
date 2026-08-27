@if (!empty($publication) && $publication->hasMedia('covers'))
    <a href="{{ $publication->getFirstMediaUrl('covers') }}"
    target="_blank">
        <img src="{{ $publication->getFirstMediaUrl('covers', 'thumb') }}"
            alt="{{ $publication->title }}"
            class="img-thumbnail">
    </a>
@endif
