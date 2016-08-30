@extends('layouts.master')

@section('title')
{{ trans('navigation.lemmas') }}
@stop

@section('content')
        <h1>{{ trans('navigation.lemmas') }}</h1>
        
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/') }}">{{ trans('messages.back_to_list') }}</a> |
            
        @if (User::checkAccess('dict.edit'))
        {{-- @can('dict.edit',$lemma) --}}
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id.'/edit') }}">
        {{-- @endcan --}}
        @endif
        {{ trans('messages.edit') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif
            | <a href="">{{ trans('messages.history') }}</a>
        </p>
        
        <h2>{{ $lemma->lemma }}</h2>
        
        <p><b>{{ trans('dict.lang') }}:</b> {{ $lemma->lang->name}}</p>
        <p><b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}</p>
        
        @foreach ($lemma->meanings as $meaning)
        <div>
            <h3>{{$meaning->meaning_n}}  {{ trans('dict.meaning') }}</h3>
            <ul>
                @foreach ($meaning_texts[$meaning->id] as $lang_name => $meaning_text)
                <li><b>{{$lang_name}}:</b> {{$meaning_text}}</li>
                @endforeach
            {{--@foreach ($meaning->meaningTexts as $meaning_text)
                <li><b>{{$meaning_text->lang->name}}:</b> {{$meaning_text->meaning_text}}</li>
            @endforeach--}}
            </ul>
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
@stop


