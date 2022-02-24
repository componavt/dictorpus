    <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <tr>
            <th rowspan="2">Маркер</th>
            <th rowspan="2">Диалектный вариант</th>
    @foreach ($gr_dialects as $gr_name => $cols)
            <th colspan="{{$cols}}">{{$gr_name}}</th>
    @endforeach
        </tr>
        <tr>
    @foreach ($dialects as $dialect_id => $info)
            <th><a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_dialect='.$dialect_id)}}">{{$info['name']}}</a></th>
    @endforeach
        </tr>
        
        <tr>
            <td colspan='2'><b>Общее количество текстов</b></td>
    @foreach ($dialects as $dialect_id => $info)
            <td style="text-align: right">{{$info['text_total']}}</td>
    @endforeach
        </tr>
        
        <tr>
            <td colspan='2'><b>Общее количество слов</b></td>
    @foreach ($dialects as $dialect_id => $info)
            <td style="text-align: right">{{$info['word_total']}}</td>
    @endforeach
        </tr>
        
    @foreach ($dmarkers as $marker)
        @if (sizeof($marker->mvariants)) 
        <tr>
            <td rowspan="{{sizeof($marker->mvariants)}}">
                <b>{{ $marker->id }}. {{ $marker->name }}</b>
            </td>
            <?php $count=1; ?>
            @foreach ( $marker->mvariants as $variant )
                @if($count >1) 
        <tr>    
                @endif
            <td><b>{{ $variant->name }}</b></td>
                @foreach ($dialects as $dialect_id => $dialect_info)
                    @if ($output == 'frequency' || $output == 'fraction')
    @include('experiments.dialect_dmarker._td_frequency')
                    @elseif ($output == 'words')
    @include('experiments.dialect_dmarker._td_words')
                    @endif
                @endforeach
                @if ( $count < sizeof($marker->mvariants) ) 
        </tr>    
                @endif
        @endforeach
        </tr>
        @endif
    @endforeach
    </table>