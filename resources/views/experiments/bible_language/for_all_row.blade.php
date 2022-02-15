        <tr>
            <th style="text-align: left">{{$title}}</th>
        @foreach($totals as $total)
            <td style="text-align: right">{{$total}} {{$sign ?? ''}}</td>
        @endforeach
        </tr>
