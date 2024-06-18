<div class="b-lemma">
@if ($lemma->hasEssentialWordforms($without_dialect))
    <div class='row'>
    <div class='col col-md-8 col-sm-12'>
@endif        
        <h2>
            {{ $lemma->lemma }}
@foreach ($lemma->audios as $audio)
                @include('ldl._audio')
@endforeach
        </h2>
@foreach ($lemma->meanings as $meaning)
        <div class="lemma-meaning">
            <div class="lemma-meaning-left">
    @if (sizeof($lemma->meanings) >1)
                    {{$meaning->meaning_n}}.
    @endif
                    {{ $meaning->textByLangCode($locale, 'ru') }}

    @if ($meaning->places()->where('id', '<>', 245)->count()) 
                    <p class='place-usage'>
                        <b>{{ trans('dict.places_use') }}</b>: 
                        {{ join(', ', $meaning->places()->where('id', '<>', 245)->get()->pluck('name')->toArray()) }}
                    </p>
    @endif
            </div>

            <div class="lemma-meaning-examples">
                <img class="img-loading" id="img-loading_{{$meaning->id}}" src="{{ asset('images/loading.gif') }}">
                <div  id="meaning-examples_{{$meaning->id}}"></div>
            </div>
        </div>
@endforeach
@if ($lemma->hasEssentialWordforms($without_dialect))
    </div>
    <div class='col wordforms-b col-md-4 col-sm-12'>
        @include('ldl._wordforms')    
    </div>
    </div>
@endif        
</div>
