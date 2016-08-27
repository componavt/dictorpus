@extends('layouts.master')

@section('title')
{{ trans('dict.list_long_lemmas') }}
@stop

@section('content')
        <h2>{{ trans('dict.list_long_lemmas') }}</h2>

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

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($lemmas)
        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.pos') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lemmas as $lemma)
            <tr>
                <td>{{$lemma->lemma}}</td>
                <td>
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                <td>
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
        {!! $lemmas->render() !!}
@stop


