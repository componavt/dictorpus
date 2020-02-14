<div class="row">    
    <div class="col-md-4">
        <p><b>Total number of word forms:</b> {{$results['total_num']}}</p> 

        <table class="table-bordered">
            <tr>
                <th>Evaluation</th>
                <th style="text-align: center">Quantity of pairs</th>
                <th style="text-align: center">Percent, %</th>
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
