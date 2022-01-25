    @if ($fragment)
        @include('corpus.sentence.fragment.view', ['id' => $id, 'text_xml'=>$fragment->text_xml])
    @else
        <button type="button" class="btn btn-info add-fragment"
                onClick="editFragment({{$id}})">
            {{trans('dict.add_fragment')}}
        </button>
    @endif
