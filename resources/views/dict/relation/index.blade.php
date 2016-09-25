@extends('layouts.master')

@section('title')
{{ trans('dict.relation_list') }}
@stop

@section('content')
        <h2>{{ trans('dict.relation_list') }}</h2>
        
        <p>
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/relation/create') }}">
        @endif
            {{ trans('messages.create_new_g') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif
        </p>
        
        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('messages.seq_num') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('dict.reverse_relation') }}</th>
                @if (User::checkAccess('dict.edit'))
                <th colspan="2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($relations as $relation)
            <tr>
                <td>{{$relation->sequence_number}}</td>
                <td>{{$relation->name_en}}</td>
                <td>{{$relation->name_ru}}</td>
                <td>
                    @if ($relation->reverse_relation)
                        {{$relation->reverse_relation->name}}
                    @endif
                </td>
                @if (User::checkAccess('dict.edit'))
                <td>
                    <a  href="{{ LaravelLocalization::localizeURL('/dict/relation/'.$relation->id.'/edit') }}" 
                        class="btn btn-warning btn-xs btn-detail" value="{{$relation->id}}">{{ trans('messages.edit') }}</a> 
                 </td>
                <td>
                    @include('widgets.form._button_delete', ['is_button'=>true, $route = 'relation.destroy', 'id' => $relation->id])
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
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/relation');
@stop


