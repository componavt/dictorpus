<div class='lemma-title'>
<h2>
    {{ucfirst($lemma->lemma)}}
    @if ($lemma->audios->first())
        @include('widgets.audio_decor', ['route'=>$lemma->audios->first()->url()])
    @endif
</h2>
    
    <!--a class='wordform-link' onClick='$(".wordforms-b").toggle(400)'>{{trans('navigation.wordforms')}}</a-->    
</div>

@if ($lemma->pos)
<p class="lemma-pos">
    {{ mb_ucfirst(trans('dict.pos')) }}: {{ $lemma->pos->name}}  
    {{$lemma->featsToString()}}
</p>
@endif

<?php $meaning_n = 1; ?>
@foreach ($lemma->meaningsWithBestExamples() as $meaning)
    <div class='meaning-b'>
            
        <div class="lemma-meaning-text">
            @if (sizeof($lemma->meaningsWithBestExamples()) >1)
            {{$meaning_n++}})
            @endif
            
            {{$meaning->textByLangCode($locale, 'ru')}}</p>
        </div>

        @if ($meaning->hasPhoto())
        <div id='meaning-photo_{{$meaning->id}}' class="meaning-photo">
        </div>
        <img class="img-loading" id="img-photo-loading_{{$meaning->id}}" src="{{ asset('images/loading.gif') }}">
        @endif  

        <div class='lemma-meaning-b'>
            <div>
            @include('olodict._meaning_sentences', ['count'=>1, 'sentences'=>$meaning->sentences(false, '', 0, 10)])
            </div>
        </div>
    </div>
    <div class='relations-b'>
        @include('olodict._relations')        
    </div>
@endforeach


@if (sizeof($phrases))
<div>
    <h3>{{trans('olodict.phrases')}}</h3> 
    <!--span class="romb"></span><span class="romb"></span><span class="romb"></span-->
    
    <div id="lemma-phrases">
        @foreach ($phrases as $ph_lemma) 
        <div class="lemma-phrase">
            @if ($ph_lemma->audios()->count())
                @include('widgets.audio_decor', ['route'=>$ph_lemma->audios()->first()->url()])
            @endif
            <span class='imp'>{{$ph_lemma->lemma}}</span> 
                - {{$ph_lemma->phraseMeaning()}}
        </div>
        @endforeach
    </div>
</div>
@endif
