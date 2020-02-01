@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<H2>Поиск по аналогии</H2>
<div class="row">
    <div class="col-sm-6">
        @include('experiments.index_pos')
    </div>
    
    <div class="col-sm-6">
        @include('experiments.index_gramset')
    </div>
</div>
@endsection