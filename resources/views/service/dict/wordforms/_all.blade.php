<h3>{{$lemma->zaikovTemplate()}}</h3>
<table class="table table-striped rwd-table wide-md">
@if ($lemma->pos->isName())
    @include('service.dict.wordforms._for_name')
@elseif ($lemma->pos->isVerb())
    @include('service.dict.wordforms._for_verb')
@endif
</table>