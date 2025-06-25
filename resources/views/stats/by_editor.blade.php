@extends('layouts.page')

@section('page_title')
{{ trans('stats.stats_by_editors') }}
@endsection

@section('headExtra')
    {!!Html::style('css/stats.css')!!}
@stop

@section('body')
    по редактору: <b>{{ $user->name }}</b> ({{ $user->rolesNames() }})<br>
    {{ $user->country }}@if ($user->city), {{ $user->city }}@endif @if ($user->affilation), {{ $user->affilation }}@endif
    
    {!! Form::open(array('method'=>'GET', 'route' => ['stats.by_editor', $user])) !!}
    <div style="display: flex; margin-top: 10px; align-items: baseline">
        <span style="margin-right: 10px">с</span>
        @include('widgets.form.formitem._DATE', 
            ['name' => 'min_date', 
             'value' => old('min_date') ? old('min_date') : ($min_date ? $min_date : date('Y-m-d')),
             'placeholder' => 'dd.mm.yyyy'])
        <span style="margin: 0 10px">по</span>
        @include('widgets.form.formitem._DATE', 
            ['name' => 'max_date', 
             'value' => old('max_date') ? old('max_date') : ($max_date ? $max_date : date('Y-m-d')),
             'placeholder' => 'dd.mm.yyyy'])
        <span style="margin: 0 10px"></span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
    {!! Form::close() !!}
    <p><a href="{{ route('stats.by_editor', $user).$quarter_query }}">В текущем квартале</a></p>
    <p><a href="{{ route('stats.by_editor', $user).$year_query }}">В текущем году</a></p>
    
    <h3>Создано</h3>
    @foreach ($models as $model => $title)
        @if(!empty($history_created[$model]))
    <p>{{ $title }}: <b>{{ format_number($history_created[$model]->count) }}</b></p>
        @endif
    @endforeach
    
    
    <h3>Изменено</h3>
    @foreach ($history_updated as $title => $count)
        @if (!empty($count))
    <p>{{ $title }}: <b>{{ format_number($count) }}</b></p>
        @endif
    @endforeach
    
    <p><a href="{{ route('stats.by_editors') }}">К списку редакторов</a></p>
@stop
