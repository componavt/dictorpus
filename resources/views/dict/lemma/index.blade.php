@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('content')
        <h2>{{ trans('navigation.lemmas') }}</h2>
        
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/sorted_by_length') }}">{{ trans('messages.list_long_lemmas') }}</a></p>

        {!! Form::open(['url' => '/dict/lemma/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_select', 
                ['name' => 'lang_id', 
                 'values' =>$lang_values,
                 'value' =>$lang_id,
                 'placeholder' => trans('dict.select_lang') ]) 
        @include('widgets.form._formitem_select', 
                ['name' => 'pos_id', 
                 'values' =>$pos_values,
                 'value' =>$pos_id,
                 'placeholder' => trans('dict.select_pos') ]) 
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $limit_num, 
                'placeholder' => trans('messages.limit_num') ])
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
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
@stop


