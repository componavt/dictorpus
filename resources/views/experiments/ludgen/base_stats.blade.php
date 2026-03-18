@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    @php
        $count = 0;
        
        $total_verbs = array_sum(array_map('array_sum', $types)) + $other_verbs->count();
    
    @endphp
    <h2>Статистика основ людиковских глаголов</h2>
    <table class='table-bordered table-wide table-striped rwd-table wide-md'>
    @foreach ($types as $type_group => $type_oks)
        <tr style="vertical-align: top">
            <th colspan="3">{{ $type_group }}</th>
        </tr>
        @foreach ($type_oks as $ok => $count) 
        <tr style="vertical-align: top">
            <td style="text-align: right">{{ $ok }}</td>
            <td style="text-align: right">{{ $count }}</td>
            <td style="text-align: right">{{ round(100 * $count / $total_verbs, 2) }}%</td>
        </tr>
        @endforeach
    @endforeach
        <tr style="vertical-align: top">
            <th colspan="3">Остальные</th>            
        </tr>
        <tr style="vertical-align: top">
            <td>{{ join(", ", $other_verbs->pluck('lemma')->toArray()) }}</td>
            <td style="text-align: right">{{ $other_verbs->count() }}</td>
            <td style="text-align: right">{{ round(100 * ($other_verbs->count()) / $total_verbs, 2) }}%</td>
        </tr>
        <tr style="vertical-align: top">
            <th>Итого</th>
            <td style="text-align: right" colspan="2">{{ array_sum(array_map('array_sum', $types)) + $other_verbs->count() }} (<b>{{ $total_verbs}}</b>)</td>
        </tr>
    </table>
        
@endsection
