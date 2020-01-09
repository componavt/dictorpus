<div class="row">    
    <div class="col-md-4">
        <p><b>Всего словоформ:</b> {{$results['total_num']}}</p> 

        <table class="table-bordered">
            <tr>
                <th rowspan="2">Оценка</th>
                <th colspan="2" style="text-align: center">Количество</th>
            </tr>
            <tr>
                <th>По отдельности</th>
                <th>По совокупности</th>
            </tr>
            @foreach ($results['eval_end'] as $v => $c) 
            <tr>
                <td style="text-align: right">{{$v}}</td>
                <td style="text-align: right">{{$c}}</td>
                <td style="text-align: right">{{$results['eval_end_gen'][$v]}}</td>
            </tr>
            @endforeach
            <tr><th colspan='3'>в процентах</td></tr>
            @foreach ($results['eval_end_proc'] as $v => $c) 
            <tr>
                <td style="text-align: right">{{$v}}</td>
                <td style="text-align: right">{{$c}}%</td>
                <td style="text-align: right">{{$results['eval_end_gen_proc'][$v]}}%</td>
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
