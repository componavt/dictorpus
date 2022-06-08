<?php $first=\Arrays::array_key_first($results['eval_proc']); ?>

<!--div class="row">    
    <div class="col-md-5"-->
        <p><b>Total number of word forms:</b> {{$results['total_num']}}</p> 

        <table class="table-bordered">
            <tr>
                <th></th>
                @foreach (array_keys($results['eval_proc'][$first]) as $eval)
                <th style="text-align: right">{{$eval}}</th>
                @endforeach
            </tr>
            @foreach ($results['eval_proc'] as $p_name => $evals) 
            <tr>
                <td>{{$p_name}}</td>
                @foreach ($evals as $k => $v) 
                <td style="text-align: right">{{$v}}</td>
                @endforeach
            </tr>
            @endforeach
        </table>
    <!--/div>
    
    <div class="col-md-7"-->
    <div style="height:300px">
        {!! $results['chart']->container() !!}
        {!! $results['chart']->script() !!}
    </div>
    <!--/div>
</div-->