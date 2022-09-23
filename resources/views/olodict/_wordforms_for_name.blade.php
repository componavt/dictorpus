    <tr><th></th><th>{{trans('olodict.singular')}}</th><th>{{trans('olodict.plural')}}</th></tr>
    @foreach($wordforms as $case_name => $number_wordforms)
    <tr>
        <th>{{$case_name}}</th>
        <td>{{isset($number_wordforms[1]) ? $number_wordforms[1] : '&mdash;'}}</td>
        <td>{{isset($number_wordforms[2]) ? $number_wordforms[2] : '&mdash;'}}</td>
    </tr>
    @endforeach