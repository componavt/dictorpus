@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/lemma.css')!!}
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
            <h3>{{$meaning->meaning_n}}  {{ trans('dict.meaning') }}</h3>
            @if (isset($meaning_texts[$meaning->id]))
            <ul>
                @foreach ($meaning_texts[$meaning->id] as $lang_name => $meaning_text)
                <li><b>{{$lang_name}}:</b> {{$meaning_text}}</li>
                @endforeach
            </ul>
            @endif

            @if (isset($meaning_relations[$meaning->id]))
            <ul>
                @foreach ($meaning_relations[$meaning->id] as $relation_name => $relation_lemmas)
                <?php
                    $rel_lemmas = [];
                    foreach ($relation_lemmas as $relation_lemma_id => $relation_lemma_info) {
                        $rel_lemmas[] = '<a href="/dict/lemma/'.$relation_lemma_id.'">'.
                                        $relation_lemma_info['lemma'].'</a> ('.
                                        $relation_lemma_info['meaning'].')';
                    }
                    if (sizeof($rel_lemmas)>1) {
                        $relation_meanings =  '<br>'. join ('<br>',$rel_lemmas);
                    } else {
                        $relation_meanings =  join ('; ',$rel_lemmas);
                    }                        
                ?>
                <p><b>{{$relation_name}}:</b> {!! $relation_meanings !!}</p>
                @endforeach
            </ul>
            @endif

            @if (isset($translation_values[$meaning->id]))
            <div class="show-meaning-translation">
            <h4>{{ trans('dict.translation')}}</h4>
                @foreach ($translation_values[$meaning->id] as $lang_text => $translation_lemmas)
                <?php
                    $transl_lemmas = [];
                    foreach ($translation_lemmas as $translation_lemma_id => $translation_lemma_info) {
                        $transl_lemmas[] = '<a href="/dict/lemma/'.$translation_lemma_id.'">'.
                                        $translation_lemma_info['lemma'].'</a> ('.
                                        $translation_lemma_info['meaning'].')';
                    }
                    if (sizeof($transl_lemmas)>1) {
                        $translation_meanings =  '<br>'. join ('<br>',$transl_lemmas);
                    } else {
                        $translation_meanings =  join ('; ',$transl_lemmas);
                    }                        
                ?>
                <p><b>{{$lang_text}}:</b> {!! $translation_meanings !!}</p>
                @endforeach
            </div>
            @endif

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
@stop

