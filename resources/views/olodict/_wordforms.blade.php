<h3 class='wordform-title'>{{trans('navigation.wordforms')}}</h3>
<table class="table-striped">
@if ($lemma->pos->isName())
    @include('olodict._wordforms_for_name')
@elseif ($lemma->pos->isVerb())
    @include('olodict._wordforms_for_verb')
@endif
</table>