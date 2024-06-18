    <tr><th></th><th>{{trans('olodict.positive')}}</th><th>{{trans('olodict.negative')}}</th></tr>
    @foreach ($wordforms as $category_name => $c_wordforms)
    <tr><th colspan='3' style='text-align: center'>{{$category_name}}</th></tr>
        @foreach ($c_wordforms as $c_name => $n_wordforms)
    <tr>
        <th style="white-space: nowrap;" colspan='{{is_array($n_wordforms) ? 1 : 2}}'>{{$c_name}}</th>
            @if (is_array($n_wordforms))
        <td>{{isset($n_wordforms[38]) ? $n_wordforms[38] : ''}}</td>
        <td>{{isset($n_wordforms[39]) ? $n_wordforms[39] : ''}}</td>
            @else
        <td>{{$n_wordforms}}</td>
            @endif
    </tr>
        @endforeach
    @endforeach