@foreach ($meaning->phrases as $phrase)
    <div id="b-phrase-{{ $phrase->id }}" style="display: inline">
        @include('service.dict.meaning._phrase')
    </div>
@endforeach