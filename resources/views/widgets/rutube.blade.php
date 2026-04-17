@if ($video)
    <iframe
    width="{{ $width ?? '100%'}}"
    height="{{ $height ?? 270 }}"
    src="https://rutube.ru/play/embed/{{ $video }}"
    style="border: none;"
    allow="clipboard-write; autoplay"
    allowFullScreen
    ></iframe>
@endif
