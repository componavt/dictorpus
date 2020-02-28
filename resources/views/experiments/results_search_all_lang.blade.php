<div class="row">    
    <div class="col-md-4">
        <table class="table-bordered">
            <tr>
                <th>Evaluation</th>
                @foreach(array_keys($results['eval']) as $lang_name)
                <th style="text-align: center">{{$lang_name}}</th>
                @endforeach
            </tr>
            @foreach ($results['eval_proc'][1] as $v => $c) 
            <tr>
                <td style="text-align: right">{{$v}}</td>
                <td style="text-align: right">{{$c}}</td>
                <td style="text-align: right">{{$results['eval_proc'][4][$v]}}</td>
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
