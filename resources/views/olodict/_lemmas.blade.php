@foreach ($lemmas as $lemma)
<div class='lemma-b'>
    <div>
    @include('olodict._lemma', ['phrases' => $lemma->phrases->sortBy('lemma'), 'meaning_texts' => $lemma->getMeaningTexts()])
    </div>
    <div class='wordforms-b'>
    @include('olodict._wordforms',['wordforms'=>$lemma->wordformsForTable($dialect_id)])    
    </div>
</div>    
@endforeach