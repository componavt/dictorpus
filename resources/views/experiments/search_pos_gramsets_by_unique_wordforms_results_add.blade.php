<div class="row">    
    <div class="col-md-4">
        <p><b>Всего словоформ:</b> {{$results['total_num']}}</p> 

        <table class="table-bordered">
            <tr>
                <th rowspan="2">Оценка</th>
                <th colspan="2" style="text-align: center">Количество</th>
            </tr>
            <tr>
                <th>По частям речи</th>
                <th>По грамсетам</th>
            </tr>
            @foreach ($results['pos_val'] as $v => $pos_c) 
            <tr>
                <td style="text-align: right">{{$v}}</td>
                <td style="text-align: right">{{$pos_c}}</td>
                <td style="text-align: right">{{$results['gram_val'][$v]}}</td>
            </tr>
            @endforeach
            <tr><th colspan='3'>в процентах</td></tr>
            @foreach ($results['pos_val_proc'] as $v => $pos_c) 
            <tr>
                <td style="text-align: right">{{$v}}</td>
                <td style="text-align: right">{{$pos_c}}%</td>
                <td style="text-align: right">{{$results['gram_val_proc'][$v]}}%</td>
            </tr>
            @endforeach
        </table>
    </div>
    
    <div class="col-md-8">
        <div id="ValuationChart">
            {!! $results['chart']->container() !!}
        </div>
        {!! $results['chart']->script() !!}
    </div>    
</div>    
