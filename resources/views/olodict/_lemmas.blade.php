@foreach ($lemmas as $lemma)
<div class='lemma-b'>
    @include('olodict._lemma', ['phrases' => $lemma->phrases->sortBy('lemma')])
</div>    
@endforeach