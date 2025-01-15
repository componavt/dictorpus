@extends('layouts.page')

@section('page_title')
{{ trans('navigation.concepts') }}
@stop

@section('headExtra')
    {!!Html::style('css/table.css')!!}
@stop

@section('body')        
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/concept_category') }}">{{ trans('navigation.concept_categories') }}</a> |
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/concept/create') }}">
        @endif
            {{ trans('messages.create_new_g') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif
        </p>

        @include('dict.concept._search_form',['url' => '/dict/concept/']) 

        @include('widgets.found_records', ['numAll'=>$numAll])
        
        @if ($numAll)                
        <table class="table table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ trans('messages.category') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('messages.in_russian') }}</th>
                <th>{{ trans('messages.in_english') }}</th>
                <th>{{ trans('dict.descr') }}</th>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('messages.photo') }}</th>
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
                <td data-th="{{ trans('messages.in_russian') }}">
                    <a href="{{ show_route($concept, $args_by_get) }}">{{$concept->text_ru}}</a>
                </td>
                <td data-th="{{ trans('messages.in_english') }}">{{$concept->text_en}}</td>
                <td data-th="{{ trans('dict.descr') }}" class="small">{{$concept->descr_ru}}</td>
                <td data-th="{{ trans('dict.lemmas') }}">
                    @if ($concept->countLemmas())
                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma') }}?search_concept={{$concept->id}}">{{$concept->countLemmas()}}</a>
                    @else
                    0
                    @endif
                </td>
                <td data-th="{{ trans('messages.photo') }}">
                    <div id='concept-photo_{{$concept->id}}' class='concept-photo'></div>                    
                    <img class="img-loading" id="img-photo-loading_{{$concept->id}}" src="{{ asset('images/loading_small.gif') }}">
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
            {!! $concepts->appends($url_args)->render() !!}
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/meaning.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}');
    @foreach($concepts as $concept)
        loadPhoto('concept', {{$concept->id}}, '/dict/concept/{{$concept->id}}/photo_preview');
    @endforeach
@stop


