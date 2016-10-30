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
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}">{{ trans('messages.back_to_list') }}</a>

        @if (User::checkAccess('dict.edit'))
        {{-- @can('dict.edit',$lemma) --}}
{{--            | @include('widgets.form._button_edit', ['route' => '/dict/lemma/'.$lemma->id.'/edit']) --}}
            | @include('widgets.form._button_delete', ['route' => 'lemma.destroy', 'id' => $lemma->id]) 
            | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}">{{ trans('messages.create_new_f') }}</a>
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif
        {{-- @endcan --}}

            | <a href="/dict/lemma/{{ $lemma->id }}/history">{{ trans('messages.history') }}</a>
        </p>

        <h2>
            {{ $lemma->lemma }}
            @include('widgets.form._button_edit', 
                     ['route' => '/dict/lemma/'.$lemma->id.'/edit',
                      'without_text' => 1])
        </h2>

        <p><b>{{ trans('dict.lang') }}:</b> {{ $lemma->lang->name}}</p>
        <p><b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}</p>

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

        <h3>
            {{ trans('dict.wordforms') }}
            @if (User::checkAccess('dict.edit'))
                @include('widgets.form._button_edit', 
                         ['route' => '/dict/lemma/'.$lemma->id.'/edit/wordforms',
                          'without_text' => 1])
            @endif
        </h3>
        @if ($lemma->wordforms()->count())
        <?php $key=1;?>
        <table class="table-bordered">
            @foreach ($lemma->wordformsWithGramsets() as $wordform)
            <tr>
                <td>{{$key++}}.</td>
                <td>{{ $wordform->wordform}}</td>
                @if($lemma->hasGramsets())
                <td>
                    {{$wordform->gramsetString}}
                </td>
                @endif
            </tr>
            @endforeach
        </table>
        @endif

        @include('dict.lemma._modal_delete')
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/lemma');
    
    $('.show-more-examples').click(function(){
        var meaning_n = $(this).attr('data-for');
        var id='more-'+meaning_n;
        $(this).hide();
        $('#'+id).show();
    });
    $('.hide-more-examples').click(function(){
        var meaning_n = $(this).attr('data-for');
        var text='more-'+meaning_n;
        var link='show-more-'+meaning_n;
        $('#'+text).hide();
        $('#'+link).show();
    });
@stop

