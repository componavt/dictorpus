@extends('layouts.page')

@section('page_title')
{{ trans('dict.relation_list') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        <p style="text-align:right">
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/relation/create') }}">
        @endif
            {{ trans('messages.create_new_g') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif
        </p>
        
        <table class="table table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th>{{ trans('messages.seq_num') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('dict.reverse_relation') }}</th>
                @if (User::checkAccess('dict.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($relations as $relation)
            <tr>
                <td data-th="{{ trans('messages.seq_num') }}">{{$relation->sequence_number}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$relation->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$relation->name_ru}}</td>
                <td data-th="{{ trans('dict.reverse_relation') }}">
                    @if ($relation->reverse_relation)
                        {{$relation->reverse_relation->name}}
                    @endif
                </td>
                @if (User::checkAccess('dict.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', ['is_button'=>true, 'route' => '/dict/relation/'.$relation->id.'/edit'])
                    @include('widgets.form.button._delete', ['is_button'=>true, $route = 'relation.destroy', 'args'=>['id' => $relation->id]])
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
    recDelete('{{ trans('messages.confirm_delete') }}');
@stop


