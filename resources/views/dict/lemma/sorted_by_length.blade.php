@extends('layouts.app')

@section('title')
{{ trans('messages.list_long_lemmas') }}
@stop

@section('content')
    <div class="container">

        <h2>{{ trans('messages.list_long_lemmas') }}</h2>

        {!! Form::open(array('url' => '/dict/lemma/sorted_by_length', 
                             'method' => 'get', 
                             'class' => 'form-inline')) 
        !!}
        {!! Form::text('limit_num', 
                       $limit_num, 
                       array('placeholder'=>trans('messages.limit_num'), 
                             'class'=>'form-control', 
                             'required'=>'true')) 
        !!} 
        {!! Form::submit(trans('messages.view'),
                               array('class'=>'btn btn-default btn-primary')) 
                !!}
        {!! Form::close() !!}

        @if ($lemmas)
        <br>
        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('messages.lemma') }}</th>
                <th>{{ trans('messages.lang') }}</th>
                <th>{{ trans('messages.pos') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma)
            <tr>
                <td>{{$lemma->lemma}}</td>
                <td>{{$lemma->lang->name}}</td>
                <td>{{$lemma->pos->name}}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
    </div>
@stop


