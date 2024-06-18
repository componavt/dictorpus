<table class="table-striped">
    <tr><td colspan="3" class='wordform-title'>{{ trans('navigation.wordforms') }}</td></tr>
@foreach ($lemma->existDialects() as $dialect_id=>$dialect_name)
    @if ($dialect_id!= $without_dialect && $lemma->hasEssentialDialectWordforms($dialect_id))
    <tr><td colspan="3" class='wordform-dialect'>{{ $dialect_name }}</td></tr>
        @if ($lemma->pos->isName())
            @include('ldl._wordforms_for_name',['wordforms'=>$lemma->wordformsForTable($dialect_id, true)])
        @elseif ($lemma->pos->isVerb())
            @include('ldl._wordforms_for_verb',['wordforms'=>$lemma->wordformsForTable($dialect_id, true)])
        @endif
    @endif
@endforeach
</table>