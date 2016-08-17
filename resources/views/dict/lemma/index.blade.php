@extends('layouts.app')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('content')
    <div class="container">

        <h2>{{ trans('navigation.lemmas') }}</h2>
        
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/sorted_by_length') }}">{{ trans('messages.list_long_lemmas') }}</a></p>

        {!! Form::open(array('url' => '/dict/lemma/', 
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
                <td><a href="lemma/{{$lemma->id}}">{{$lemma->lemma}}</a></td>
                <td>{{$lemma->lang->name}}</td>
                <td>{{$lemma->pos->name}}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
    </div>
@stop


