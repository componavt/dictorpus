<table class="lemma-examples">
@foreach ($sentences as $sentence)
    <tr class="row" id="sentence-{{$meaning->id.'_'.$sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id']}}">
        <td> 
            {{ $count++ }}.
            @include('dict.lemma.example.sentence', 
                ['relevance'=>$sentence['relevance'], 'is_edit' => $is_edit])
        </td>
    </tr>
@endforeach
</table>
