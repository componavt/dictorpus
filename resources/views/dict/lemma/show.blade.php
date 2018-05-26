@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/text.css')!!}
@stop

@section('content')
        <h1>{{ trans('navigation.lemmas') }}</h1>

        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>

        @if (User::checkAccess('dict.edit'))
            | @include('widgets.form._button_delete', 
                       ['route' => 'lemma.destroy', 
                        'id' => $lemma->id]) 
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">{{ trans('messages.create_new_f') }}</a>
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif

            | <a href="/dict/lemma/{{ $lemma->id }}/history{{$args_by_get}}">{{ trans('messages.history') }}</a>
        </p>

        <h2>
            {{ $lemma->lemma }}
            @if (User::checkAccess('dict.edit'))
                @include('widgets.form._button_edit', 
                         ['route' => '/dict/lemma/'.$lemma->id.'/edit',
                          'without_text' => 1])
            @endif
        </h2>

        <p><b>{{ trans('dict.lang') }}:</b> {{ $lemma->lang->name}}</p>
        <p>
            <b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}
            @if ($lemma->reflexive)
                ({{ trans('dict.reflexive_verb') }})
            @endif
        </p>

        @foreach ($lemma->meanings as $meaning)
        <table class="table lemma-meaning">
            <tr>
                <td>
                    <h3>{{$meaning->meaning_n}}  {{ trans('dict.meaning') }}</h3>

                    @include('dict.lemma.show.meaning_texts')

                    @include('dict.lemma.show.meaning_relations')

                    @include('dict.lemma.show.meaning_translations')
                </td>
                <td>
                    @include('dict.lemma.show.examples')
                </td>
            </tr>
        </table>
        @endforeach

        @if ($lemma->isChangeable())
            @include('dict.lemma.show.wordforms')
        @endif
            
        @include('dict.lemma._modal_delete')
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/meaning.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/lemma{{$args_by_get}}');
    toggleExamples();
    addExample('{{LaravelLocalization::localizeURL('/dict/lemma/add/example')}}');
    removeExample('{{LaravelLocalization::localizeURL('/dict/lemma/remove/example')}}');
@stop

