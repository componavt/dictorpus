    @foreach ($members as $meaning)
        @include('/dict/synset/_meaning_row', ['syntype_id'=>\App\Models\Dict\Syntype::TYPE_FULL])
    @endforeach
