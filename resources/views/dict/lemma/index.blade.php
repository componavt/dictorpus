@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('content')
        <h2>{{ trans('navigation.lemmas') }}</h2>
        
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/sorted_by_length') }}">{{ trans('dict.list_long_lemmas') }}</a> 
            |
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif

        </p>

        {!! Form::open(['url' => '/dict/lemma/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_text', 
                ['name' => 'lemma_name', 
                'value' => $lemma_name,
                'size' => 15,
                'placeholder'=>trans('dict.lemma')])
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
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        
        {{trans('messages.show_by')}}
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $limit_num, 
                'size' => 5,
                'placeholder' => trans('messages.limit_num') ]) {{ trans('messages.records') }}
        {!! Form::close() !!}

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($lemmas)
        <br>
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
        {!! $lemmas->render() !!}
        @endif
@stop


