@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

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
            <td colspan="2"></td>
            <td>Основа 1 (слабая гласная основа)</td>
        </tr>
        
        @php $count = 1; @endphp
        
        @foreach ($bases as $lemma_id => list($lemma, $base))
        <tr>
            <td>{{ $count++ }}</td>
            <td><a href="{{ route('lemma.show',$lemma_id) }}">{{ $lemma }}</a></td>
            <td>{{ $base }}</td>
        </tr>
        @endforeach
    </table>
    
@endsection
