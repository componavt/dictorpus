@extends('layouts.master')

@section('title')
{{ trans('dict.dialect_list') }}
@stop

@section('content')
        <h2>{{ trans('dict.dialect_list') }}</h2>
            
        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/dialect/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>

        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('dict.code') }}</th>
                <th>{{ trans('dict.wordforms') }}</th>                
                <th>{{ trans('navigation.texts') }}</th>                
                @if (User::checkAccess('ref.edit'))
                <th colspan="2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($dialects as $dialect)
            <tr>
                <td>{{$dialect->lang->name}}</td>
                <td>{{$dialect->name_en}}</td>
                <td>{{$dialect->name_ru}}</td>
                <td>{{$dialect->code}}</td>
                <td>{{$dialect->wordforms()->count()}}</td>
                <td>{{$dialect->texts()->count()}}</td>

                @if (User::checkAccess('ref.edit'))
                <td>
                    @include('widgets.form._button_edit', ['is_button'=>true, 'route' => '/dict/dialect/'.$dialect->id.'/edit'])
                </td>
                <td>
                    @include('widgets.form._button_delete', ['is_button'=>true, 'route' => 'dialect.destroy', 'id' => $dialect->id])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/lemma');
@stop

