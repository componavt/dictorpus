{!! $text->corpusesToString('') !!} /
{{ $text->authorsToString() ? $text->authorsToString().'.' : '' }}
<a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id) }}?{{--{{$args_by_get}}&--}}{{$wid_for_link}}">{{$text->title}}</a>
@if ($text->transtext)
    / {{$text->transtext->title}}
@endif

<span class='small'>
@if ($text->event)
    (@include('corpus.place._to_string',
        ['place' => $text->event->place, 'lang_id' => $text->lang_id]
    )@if ($text->event->date), {{ $text->event->date }}@endif)

@elseif ($text->source && $text->source->bookToString())
    ({{$text->source->bookToString()}})
@endif
</span>
