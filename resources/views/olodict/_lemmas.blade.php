@foreach ($lemmas as $lemma)
<div class='lemma-b row'>
    <div class='col col-md-6 col-sm-12'>
    @include('olodict._lemma', ['phrases' => $lemma->phrases->sortBy('lemma'), 'meaning_texts' => $lemma->getMeaningTexts()])
    </div>
    <div class='col wordforms-b col-md-6 col-sm-12'>
    @include('olodict._wordforms',['wordforms'=>$lemma->wordformsForTable($dialect_id)])    
    </div>
</div>    
@endforeach