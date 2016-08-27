@extends('layouts.master')

@section('title')
{{ trans('navigation.wordforms') }}
@stop

@section('content')
        <h1>{{ trans('navigation.wordforms') }}</h1>
        <h2>{{ trans('dict.wordforms_linked_many_lemmas') }}</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/wordform/') }}">{{ trans('messages.back_to_list') }}</a></p>
      
        {!! Form::open(['url' => '/dict/wordform/with_multiple_lemmas', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
        @include('widgets.form._formitem_text', 
                ['name' => 'wordform_name', 
                'value' => $wordform_name,
                'placeholder'=>trans('dict.wordform')])
        @include('widgets.form._formitem_select', 
                ['name' => 'lang_id', 
                 'values' =>$lang_values,
                 'value' =>$lang_id,
                 'placeholder' => trans('dict.select_lang') ]) 
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
        {!! Form::close() !!}
        
        <p>{{ trans('messages.founded_records', ['count'=>count($wordforms)]) }}</p>

        @if ($wordforms)
        <table class="table">
        <thead>
            <tr>
                <th>{{ trans('dict.wordform') }}</th>
                <th>{{ trans('dict.gram_attr') }}</th>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.pos') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wordforms as $row)
                <?php $wordform=\App\Models\Dict\Wordform::find($row->wordform_id);?>
                <?php $lemmas = $wordform->lemmas;?>
                @foreach($lemmas as $key=>$lemma) 
            <tr>
                    @if ($key==0)
                <td rowspan='{{$lemmas->count()}}'>{{$wordform->wordform}}</td>
                    @endif
                <td>
                    @if ($wordform->lemmaDialectGramset($lemma->id))
                    {{ $wordform->lemmaDialectGramset($lemma->id)->gramsetString()}}
                    @endif
                </td>
                <td>
                    {{$key+1}}. <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}">{{$lemma->lemma}}</a>
                </td>
                <td>
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                <td>
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                    @endif
                </td>
            </tr>
                @endforeach
            @endforeach
        </tbody>
        </table>
        @endif
@stop


