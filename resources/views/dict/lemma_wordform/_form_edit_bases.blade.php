    <h3>{{trans('dict.bases')}}</h3>
    <table class="table-wide">
    @foreach ($base_list as $base_n=>$base_title)
            <tr>
                <th style='vertical-align: middle; text-align: left'>{{$base_n}}. {{$base_title}}</th>
                <td>
                    @include('widgets.form.formitem._text', 
                       ['name' => 'bases['.$base_n.']', 
                        'special_symbol' => true,
                        'value'=> (string)$lemma->getBase($base_n, $dialect_id)])
                </td>
           </tr>
    @endforeach
    </table>
