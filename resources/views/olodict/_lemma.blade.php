<div class='lemma-title'>
<h2>
    {{$lemma->lemma}}
    @if ($lemma->audios->first())
        @include('widgets.audio_simple', ['route'=>$lemma->audios->first()->url()])
    @endif
</h2>
    
    <a class='wordform-link' onClick=''>{{trans('navigation.wordforms')}}</a>    
</div>

@if ($lemma->pos)
<p>
    <b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}  
    {{$lemma->featsToString()}}
</p>
@endif

@foreach ($lemma->meanings as $meaning)
    <h3>{{$meaning->meaning_n}}  {{ trans('dict.meaning') }}</h3>
    <div class='lemma-meaning-b'>
        <p>{{$meaning->textByLangCode($locale)}}</p>
        @include('olodict._meaning_sentences', ['count'=>1, 'sentences'=>$meaning->sentences(false, '', 0, 10)])
    </div>
@endforeach


@if (sizeof($phrases))
<div>
    <b>{{mb_ucfirst(trans('dict.phrases'))}}</b> 

    <div id="lemma-phrases">
        @foreach ($phrases as $ph_lemma) 
        <div>
            @if ($ph_lemma->audios()->count())
                @include('widgets.audio_simple', ['route'=>$ph_lemma->audios()->first()->url()])
            @endif
            <span class='imp'>{{$ph_lemma->lemma}}</span> 
                - {{$ph_lemma->phraseMeaning()}}
        </div>
        @endforeach
    </div>
</div>
@endif
