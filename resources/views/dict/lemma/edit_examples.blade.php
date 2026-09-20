<?php
    $styles = [
        '0'  => 'relevance-0',
        '1'  => 'relevance-1',
        '3'  => 'relevance-3',
        '5'  => 'relevance-5',
        '7'  => 'relevance-7',
        '10' => 'relevance-10',
    ];
?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!! css('lemma') !!}
    {!! css('text') !!}
@stop

@section('body')
        <h2>{{ trans('messages.editing')}} {{ trans('dict.of_lemma')}}: {{ $lemma->lemma}}</h2>
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a></p>
        
        <p><b>{{ trans('dict.lang') }}:</b> {{ $lemma->lang->name}}</p>
        <p><b>{{ trans('dict.pos') }}:</b> {{ $lemma->pos->name}}</p>

        <h3>{{ trans('messages.examples') }}: {{ $sentences->total() }}</h3>
        
        {!! Form::open(['method' => 'GET', 'url' => url()->current(), 'class' => 'lemma-example-filter', 'id' => 'lemma_example_filter_form']) !!}

        @include('widgets.form._url_args_by_post', ['url_args' => $filter_url_args])

        <label>
            <input type="checkbox" name="show_checked" value="1"  {{ $show_checked  ? 'checked="checked"' : '' }} >
            {{ trans('dict.show_checked') }}
        </label>

        <button type="submit" class="btn btn-default">
            {{ trans('messages.apply') }}
        </button>

        {!! Form::close() !!}

<?php $count = ($sentences->currentPage() - 1) * $sentences->perPage() + 1; ?>
        
        @if ($sentences->lastPage() > 1)
        <div class="lemma-example-pagination">
            {!! $sentences->render() !!}
        </div>
        @endif        
        
        @if ($sentences->count())

        {!! Form::model($lemma, ['method'=>'POST', 'route' => ['lemma.update.examples', $lemma->id], 'id' => 'edit_examples_form']) !!}
        
        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
        <input type="hidden" name="back_to_url" value="{{$back_to_url}}">
        
        <table class="table lemma-example-edit">
            <tr>
                <th>{{trans('corpus.sentences')}}</th>
                
                @foreach ($meanings as $meaning)
                <td class="lemma-example-edit-right">
                    {{$meaning->meaning_n}} {{trans('dict.meaning')}}
                    @if (isset($meaning_texts[$meaning->id]))
                        @foreach ($meaning_texts[$meaning->id] as $lang_name => $meaning_text)
                        <p><b>{{$lang_name}}:</b> {{$meaning_text}}</p>
                        @endforeach
                    @endif
                </td>
                @endforeach
            </tr>
            @foreach ($sentences as $sentence)
            <tr class="{{ $sentence['example_status'] == 'conflict' ? 'lemma-example-conflict' : '' }}">
                <td>
                    {{ $count++ }}.
                    
                    @if ($sentence['example_status'] == 'conflict')
                        <span
                            class="lemma-example-conflict-mark"
                            title="У этого примера выбрано более одного значения"
                        >⚠</span>
                    @endif

                    @include('dict.lemma.example.sentence')
                </td>
                @foreach ($meanings as $meaning)
                <td>
                <?php
                    $relevance_value = isset($sentence['relevance'][$meaning->id]) ? (int)$sentence['relevance'][$meaning->id] : 1;
                    $relevance_style = isset($styles[$relevance_value]) ? $styles[$relevance_value]  : 'relevance-1';
                ?>
                    @include('widgets.form.formitem._select',
                            ['name' => 'relevance['.$meaning->id.'_'.$sentence['text']->id.'_'.$sentence['s_id'].'_'.$sentence['w_id'].']',
                             'values' => trans('dict.relevance_scope'),
                             'value' => $sentence['relevance'][$meaning->id] ?? 1,
                             'attributes' => ['class' => 'form-control js-example-relevance ' . $relevance_style]
                    ])
                </td>
                @endforeach
            </tr>
            @endforeach
        </table>
        @if ($sentences->lastPage() > 1)
        <div class="lemma-example-pagination">
            {!! $sentences->render() !!}
        </div>
        @endif

        <div class="form-group lemma-example-save-buttons">
            <button type="submit" name="after_save"  value="continue" class="btn btn-primary"
            >
                {{ trans('dict.save_and_continue') }}
            </button>

            <button
                type="submit"
                name="after_save"
                value="show"
                class="btn btn-default"
            >
                {{ trans('dict.save_and_return_to_show') }}
            </button>
        </div>
        
        {!! Form::close() !!}
        @endif
@stop

@section('footScriptExtra')
    {!! js('text') !!}
    {!! js('lemma') !!}
@stop

@section('jqueryFunc')
    showLemmaLinked();  
    
    initEditExamplesUnsavedWarning(
        {!! json_encode(trans('dict.unsaved_examples_confirm')) !!}
    );
    
    initExampleRelevanceStyles();
@stop
