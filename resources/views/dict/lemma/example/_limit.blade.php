<table class="lemma-examples">
@foreach ($sentences as $sentence)
    <tr class="row" id="sentence-{{$meaning->id.'_'.$sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id']}}">
        <td> 
            {{ $count++ }}.
            @include('dict.lemma.example.sentence', 
                ['relevance'=>$sentence['relevance'], 'is_edit' => 1])
        </td>
    </tr>
@endforeach
</table>

@if ($count<=$sentence_count)
<a {{--id="show-more-{{$meaning->id}}" --}}
   class="show-more-examples"
   onClick ="showMoreExamples(this, {{$start+$limit}}, '{{LaravelLocalization::getCurrentLocale()}}')"
   data-for="{{$meaning->id}}">
        {{ trans('dict.more_examples') }}
</a>
@endif
