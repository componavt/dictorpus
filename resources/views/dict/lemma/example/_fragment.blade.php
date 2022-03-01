    @if (isset($fragment) && $fragment)
        @include('corpus.sentence.fragment.view', 
            ['sentence_id' => $fragment->sentence_id, 
             'w_id'=>$fragment->w_id, 
             'text_xml'=>$fragment->text_xml])
    @else
        <button type="button" class="btn btn-info add-fragment"
                onClick="editFragment({{$sentence_id}}, {{$w_id}})">
            {{trans('dict.add_fragment')}}
        </button>
    @endif
