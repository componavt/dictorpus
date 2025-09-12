    @foreach ($potential_members as $meaning)
        @include('/dict/synset/_potential_row', ['syntype_id'=>\App\Models\Dict\Syntype::TYPE_FULL])
    @endforeach
