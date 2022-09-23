<table class="table-bordered table-striped">
@if ($lemma->pos->isName())
    @include('olodict._wordforms_for_name')
@elseif ($lemma->pos->isVerb())
    @include('olodict._wordforms_for_verb')
@endif
</table>