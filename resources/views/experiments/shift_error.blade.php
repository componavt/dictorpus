    <table class="table-bordered">
            <tr>
                <th>&nbsp;</th>
                @foreach (array_slice(array_keys($results['list']), 0, $results['limit'], true) as $p)
                <th>{{$p}}</th>
                @endforeach
            </tr>
            @foreach (array_keys($results['list']) as $p2)
            <tr>
                <th>{{$p2}}</th>
                @foreach (array_slice(array_keys($results['list']), 0, $results['limit'], true) as $p1)
                <td>{{!isset($results['list'][$p1][$p2]) ? '&nbsp;' : $results['list'][$p1][$p2]}}</td>
                @endforeach            
            </tr>
            @endforeach            
        </table>
        
