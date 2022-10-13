<div class='lemma-title'>
<h2>
    {{$lemma->lemma}}
    @if ($lemma->audios->first())
        @include('widgets.audio_simple', ['route'=>$lemma->audios->first()->url()])
    @endif
</h2>
    
    <a class='wordform-link' onClick='$(".wordforms-b").toggle(400)'>{{trans('navigation.wordforms')}}</a>    
</div>

@if ($lemma->pos)
<p>
    <b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}  
    {{$lemma->featsToString()}}
</p>
@endif

<?php $meaning_n = 1; ?>
@foreach ($lemma->meaningsWithBestExamples() as $meaning)
    <p>
        @if (sizeof($lemma->meaningsWithBestExamples()) >1)
        {{$meaning_n++}})
        @endif
        {{$meaning->textByLangCode($locale, 'ru')}}</p>
    <div class='lemma-meaning-b'>
        @include('olodict._meaning_sentences', ['count'=>1, 'sentences'=>$meaning->sentences(false, '', 0, 10)])
    </div>
    <div class='relations-b'>
        @include('olodict._relations')        
    </div>
@endforeach


@if (sizeof($phrases))
<div>
    <b>{{--mb_ucfirst(trans('dict.phrases'))--}}</b> 
    <span class="romb"></span><span class="romb"></span><span class="romb"></span>
    
    <div id="lemma-phrases">
        @foreach ($phrases as $ph_lemma) 
        <div class="lemma-phrase">
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
