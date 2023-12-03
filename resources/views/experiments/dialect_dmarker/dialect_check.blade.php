@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Определение диалектной принадлежности</h2>
    
    @if ($output == 'frequency' || $output == 'fraction')
        @include('experiments.dialect_dmarker._preambula')
    @endif
    
    @include('experiments.dialect_dmarker._total_table')
@endsection