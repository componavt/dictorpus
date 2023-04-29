    <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <tr>
            <th rowspan="2">Маркер</th>
            <th rowspan="2">Диалектный вариант</th>
    @foreach ($gr_dialects as $gr_name => $cols)
            <th colspan="{{2*$cols}}">{{$gr_name}}</th>
    @endforeach
        </tr>
        <tr>
    @foreach ($dialects as $dialect_id => $info)
            <th colspan='2'><a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_dialect='.$dialect_id)}}">{{$info['name']}}</a></th>
    @endforeach
        </tr>
        
        <tr>
            <td colspan='2'><b>Общее количество текстов / слов</b></td>
    @foreach ($dialects as $dialect_id => $info)
            <td style="text-align: right">{{$info['text_total']}}</td>
            <td style="text-align: right">{{$info['word_total']}}</td>
    @endforeach
        </tr>
        
    @foreach ($dmarkers as $marker)
        @if (sizeof($marker->mvariants)) 
        <tr style="vertical-align: top">
            <td rowspan="{{sizeof($marker->mvariants)}}">
                <b>{{ $marker->id }}. {{ $marker->name }}</b>
            </td>
            <?php $count=1; ?>
            @foreach ( $marker->mvariant_dialect as $variant_id => $variant_info )
                @if($count >1) 
        <tr>    
                @endif
            <td style="vertical-align: top"><b>{{ $variant_info['name'] }}</b></td>
                @foreach ($dialects as $dialect_id => $dialect_info)
                    @include('experiments.dialect_dmarker._td_'.$output, [
                        'template'=>$variant_info['template'], 
                        'variant'=>$variant_info['dialects'][$dialect_id]])
                @endforeach
                @if ( $count < sizeof($marker->mvariants) ) 
        </tr>    
                @endif
        @endforeach
        </tr>
        @endif
    @endforeach
    </table>