    <tr>
        <th></th>
@if (!$lemma->features || $lemma->features->number != 1)   
        <th>{{trans('olodict.singular')}}</th>
@endif
@if (!$lemma->features || $lemma->features->number != 2)   
        <th>{{trans('olodict.plural')}}</th>
@endif
    </tr>
@foreach($wordforms as $case_name => $number_wordforms)
    @if (!empty($number_wordforms[1]) || !empty($number_wordforms[2]))
    <tr>
        <th>{{mb_ucfirst($case_name)}}</th>
        @if (!$lemma->features || $lemma->features->number != 1)   
        <td>{{isset($number_wordforms[1]) ? $number_wordforms[1] : '&mdash;'}}</td>
        @endif
        @if (!$lemma->features || $lemma->features->number != 2)   
        <td>{{isset($number_wordforms[2]) ? $number_wordforms[2] : '&mdash;'}}</td>
        @endif
    @endif
    </tr>
@endforeach