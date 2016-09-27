@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('content')
        <h1>{{ trans('navigation.lemmas') }}</h1>

        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}">{{ trans('messages.back_to_list') }}</a>

        @if (User::checkAccess('dict.edit'))
        {{-- @can('dict.edit',$lemma) --}}
            | @include('widgets.form._button_edit', ['route' => '/dict/lemma/'.$lemma->id.'/edit'])
            | @include('widgets.form._button_delete', ['route' => 'lemma.destroy', 'id' => $lemma->id]) 
        @else
            | {{ trans('messages.edit') }} | {{ trans('messages.delete') }}
        @endif
        {{-- @endcan --}}
            | <a href="">{{ trans('messages.history') }}</a>
        </p>

        <h2>{{ $lemma->lemma }}</h2>

        <p><b>{{ trans('dict.lang') }}:</b> {{ $lemma->lang->name}}</p>
        <p><b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}</p>

        @foreach ($lemma->meanings as $meaning)
        <div>
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

        </div>
        @endforeach

        @if ($lemma->wordforms()->count())
        <h3>{{ trans('dict.wordforms') }}</h3>
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

        @if (User::checkAccess('dict.edit'))
        <p>

            {{-- @include('dict.lemma._form_delete', ['lemma'=>$lemma]) --}}
        </p>
        @endif

        @include('dict.lemma._modal_delete')
@stop

@section('footScriptExtra')
    {!!Html::script('js/rec-delete-link.js')!!}
@stop

@section('jqueryFunc')
    recDelete('{{ trans('messages.confirm_delete') }}', '/dict/lemma');
@stop

