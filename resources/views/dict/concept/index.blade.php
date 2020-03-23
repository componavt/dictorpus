@extends('layouts.page')

@section('page_title')
{{ trans('navigation.concepts') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        <p style="text-align:right">
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/concept/create') }}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif
        </p>
        
        <table class="table table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ trans('messages.category') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('dict.lemmas') }}</th>
                @if (User::checkAccess('dict.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($concepts as $concept)
            <tr>
                <td data-th="ID">{{$concept->id}}</td>
                <td data-th="{{ trans('messages.category') }}">{{$concept->concept_category_id}}</td>
                <td data-th="{{ trans('dict.pos') }}">{{$concept->pos ? $concept->pos->name: ''}}</td>
                <td data-th="{{ trans('messages.in_english') }}">{{$concept->text_en}}</td>
                <td data-th="{{ trans('messages.in_russian') }}">{{$concept->text_ru}}</td>
                <td data-th="{{ trans('dict.lemmas') }}">
                    @if ($concept->countLemmas())
                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}?search_concept={{$concept->id}}">{{$concept->countLemmas()}}</a>
                    @else
                    0
                    @endif
                </td>
                @if (User::checkAccess('dict.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', [
                        'is_button'=>true, 
                        'without_text' => 1,
                        'route' => '/dict/concept/'.$concept->id.'/edit'])
                    @include('widgets.form.button._delete', [
                        'is_button'=>true, 
                        'without_text' => 1,
                        'route' => 'concept.destroy', 
                        'args'=>['id' => $concept->id]])
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


