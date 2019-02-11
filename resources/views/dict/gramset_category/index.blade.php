@extends('layouts.page')

@section('page_title')
{{ trans('navigation.gramset_categories') }}
@stop

@section('body')
        <p style="text-align: right">
        @if (User::checkAccess('ref.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/gramset_category/create') }}">
        @endif
            {{ trans('messages.create_new_m') }}
        @if (User::checkAccess('ref.edit'))
            </a>
        @endif
        </p>
        
    <table class="table-bordered table-wide rwd-table wide-lg">
        <thead>
            <tr>
                <th>{{ trans('messages.sequence_number') }}</th>
                <th>{{ trans('dict.pos_category') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                @if (User::checkAccess('ref.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($gramset_categories as $gramset_category)
            <tr>
                <td data-th="{{ trans('messages.sequence_number') }}">{{$gramset_category->sequence_number}}</td>
                <td data-th="{{ trans('dict.pos_category') }}">{{isset(trans('dict.pos_categories')[$gramset_category->pos_category_id]) ? trans('dict.pos_categories')[$gramset_category->pos_category_id] : ''}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$gramset_category->name_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$gramset_category->name_ru}}</td>

                @if (User::checkAccess('ref.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                             'route' => '/dict/gramset_category/'.$gramset_category->id.'/edit'])
                    @include('widgets.form.button._delete', 
                            ['is_button'=>true, 
                             'without_text' => 1,
                            'route' => 'gramset_category.destroy', 
                            'id' => $gramset_category->id])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/gramset_category');
@stop

