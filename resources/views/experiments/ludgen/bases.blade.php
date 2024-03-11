@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('headExtra')
    {!!Html::style('css/history.css')!!}
@stop

@section('body')
    <h2>Генерация словоформ для людиковского наречия карельского языка на примере Святозерского диалекта</h2>
    <h3>
    @if ($what == 'verbs')
        Глаголы
    @else 
        Именные части речи
    @endif
    </h3>

    <table class='table-bordered table-wide table-striped rwd-table wide-md'>
        <tr>
            <th colspan="2"></th>
        @foreach (array_values($gramsets) as $gr)
            <th>{{ $gr }}</th>
        @endforeach
        </tr>
        
        @php $count = 1; @endphp
        
        @foreach ($wordforms as $lemma_id => $lemma_wordforms)
        <tr>
            <td>{{ $count++ }}</td>
            <td>{{ $dict_forms[$lemma_id] }}</td>
            @foreach ($lemma_wordforms as $gramset_id => $lw)
            <td>
                @if ($gramset_id == 1)
                    <a href="{{ route('lemma.show',$lemma_id) }}">
                @endif 
                @foreach ($lw as $wordform)
                <b>{{ $wordform[0] }}</b>{{ $wordform[1] }}<b>{{ $wordform[2] }}</b><br>
                @endforeach                
                @if ($gramset_id == 1)
                    </a>
                @endif 
            </td>
            @endforeach
        </tr>
        @endforeach
    </table>
    
    <table class='table-bordered table-wide table-striped rwd-table wide-md'>
        <tr>
            <td colspan="2"></td>
            <td colspan="2">Основа 1 (слабая гласная основа)</td>
        </tr>
        
        @php $count = 1; @endphp
        
        @foreach ($bases as $lemma_id => $lemma_bases)
        <tr>
            <td>{{ $count++ }}</td>
            <td><a href="{{ route('lemma.show',$lemma_id) }}">{{ $lemma_bases[0] }}</a></td>
            <td>{{ $lemma_bases[1] }}</td>
            <td>
                {!! str_diff($lemma_bases[0], $lemma_bases[1]) !!}
            </td>
        </tr>
        @endforeach
    </table>
    
@endsection
