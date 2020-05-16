        <table class="table-bordered">
            <tr>
                <th>Length</th>
                @foreach ($results['len_list'] as $l)
                <th>{{$l}}</th>
                @endforeach
            </tr>
            @foreach ($results['p_list'] as $p_name => $p_info)
            <tr>
                <th>{{$p_name}}</th>
                <?php $max = max($p_info);?>
                @foreach ($p_info as $len => $count)
                <td<?php print $count==$max ? ' style="color: red; fonr-weight: bold;"' : ''; ?>>
                    {{$count}}</td>
                @endforeach            
            </tr>
            @endforeach            
        </table>
        
        <div id="ValuationChart">
            {!! $results['chart']->container() !!}
        </div>
        {!! $results['chart']->script() !!}
