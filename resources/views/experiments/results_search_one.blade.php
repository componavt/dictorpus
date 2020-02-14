<div class="row">    
    <div class="col-md-4">
        <p><b>Всего словоформ:</b> {{$results['total_num']}}</p> 

        <table class="table-bordered">
            <tr>
                <th rowspan="2">Evaluation</th>
                <th colspan="2" style="text-align: center">Quantity of pairs</th>
                <th colspan="2" style="text-align: center">Percent, %</th>
            </tr>
            <tr>
                <th>All parts of speech</th>
            </tr>
            @foreach ($results['eval1'] as $v => $c) 
            <tr>
                <td style="text-align: right">{{$v}}</td>
                <td style="text-align: right">{{$c}}</td>
                <td style="text-align: right">{{$results['eval1_proc'][$v]}}</td>
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
