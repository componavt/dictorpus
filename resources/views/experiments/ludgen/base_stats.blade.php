@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    @php
        $total_count = [0, 0];
    @endphp
    <h2>Статистика основ людиковских глаголов</h2>
    <table class='table-bordered table-wide table-striped rwd-table wide-md'>
        <tr style="vertical-align: top">
            <th rowspan="2">Тип</th>
            <th colspan="2">В словаре</th>
            <th colspan="2">В корпусе</th>
        </tr>
        <tr style="vertical-align: top">
            <th>Количество</th>
            <th>%</th>
            <th>Количество</th>
            <th>%</th>
        </tr>

    @foreach ($types as $type_group => $type_oks)
        <tr style="vertical-align: top">
            <th colspan="5">{{ $type_group }}</th>
        </tr>
        @foreach ($type_oks as $ok => $count) 
        <tr style="vertical-align: top">
            <td style="text-align: right">{{ $ok }}</td>
            <td style="text-align: right">{{ $count }}</td>
            <td style="text-align: right">{{ round(100 * $count / $total_verbs[0], 2) }}%</td>
            <td style="text-align: right">{{ $ctypes[$type_group][$ok] }}</td>
            <td style="text-align: right">{{ round(100 * $ctypes[$type_group][$ok] / $total_verbs[1], 2) }}%</td>
        </tr>
        @php
            $total_count[0] += $count;
            $total_count[1] += $ctypes[$type_group][$ok];
        @endphp
        @endforeach
    @endforeach
        <tr style="vertical-align: top">
            <th colspan="5">Остальные</th>            
        </tr>
        <tr style="vertical-align: top">
            <td></td>
            <td style="text-align: right">{{ $other_verbs[0]->count() }}</td>
            <td style="text-align: right">{{ round(100 * ($other_verbs[0]->count()) / $total_verbs[0], 2) }}%</td>
            <td style="text-align: right">{{ $other_verbs[1]->count() }}</td>
            <td style="text-align: right">{{ round(100 * ($other_verbs[1]->count()) / $total_verbs[1], 2) }}%</td>
        </tr>
        <tr style="vertical-align: top">
            <th>Итого</th>
            <td style="text-align: right" colspan="2">{{ $total_count[0] + $other_verbs[0]->count() }} (<b>{{ $total_verbs[0] }}</b>)</td>
            <td style="text-align: right" colspan="2">{{ $total_count[1] + $other_verbs[1]->count() }} (<b>{{ $total_verbs[1] }}</b>)</td>
        </tr>
    </table>

    <h3>Другие глаголы в словаре:</h3>
    {{ join(", ", $other_verbs[0]->toArray()) }}
    
    <h3>Другие глаголы в корпусе:</h3>
    {{ join(", ", $other_verbs[1]->toArray()) }}
        
@endsection
