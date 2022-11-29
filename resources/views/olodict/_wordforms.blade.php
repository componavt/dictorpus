<table class="table-striped">
    <tr><td colspan="3" class='wordform-title'>{{trans('navigation.wordforms')}}</td></tr>
@if ($lemma->pos->isName())
    @include('olodict._wordforms_for_name')
@elseif ($lemma->pos->isVerb())
    @include('olodict._wordforms_for_verb')
@endif
</table>