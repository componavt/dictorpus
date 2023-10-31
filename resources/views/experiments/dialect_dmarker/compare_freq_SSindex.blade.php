@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Определение диалектной принадлежности</h2>
    <h3>Сравнение относительных частот и индекса Шепли-Шубика</h3>
    
    @foreach ($dialect_markers as $dialect_name => $dialect_markers)
    <h4>{{ $dialect_name }} диалект</h4>
    <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <tr>
            <th>Маркер</th>
            <th>Диалектный вариант</th>
            <th>id</th>
            <th>Относит. частота</th>
            <th>Индекс Шепли-Шубика</th>
        </tr>
        
        @foreach ($dialect_markers as $marker_name=>$marker_info)
            @if (sizeof($marker_info)) 
        <tr style="vertical-align: top">
            <td rowspan="{{sizeof($marker_info)}}">
                <b>{{ $marker_name }}</b>
            </td>
                <?php $count=1; ?>
                @foreach ( $marker_info as $variant_id => $variant_info )
                    @if($count >1) 
        </tr>
        <tr>    
                    @endif
            <td style="vertical-align: top"><b>{{ $variant_info['name'] }}</b></td>
            <td style="vertical-align: top">{{ $variant_id }}</td>
            <td style="vertical-align: top">{{ $variant_info['w_fraction'] }}</td>
            <td style="vertical-align: top">{{ $variant_info['SSindex'] }}</td>
                <?php $count++; ?>            
                @endforeach
        </tr>
            @endif
        @endforeach
        
    </table>
    @endforeach
@endsection