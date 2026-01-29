<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.lemmas') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/lemma.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')   
<div class="row">
        @include('widgets.modal',['name'=>'modalHelp',
                                  'title'=>trans('navigation.help'),
                                  'modal_view'=>'help.lemma._search'])
                                  
        <p>
            <a href="{{ route('lemma.by_wordforms') }}">{{ trans('dict.search_lemmas_by_wordforms') }}</a> 
            |
            <a href="{{ route('lemma.sorted_by_length') }}">{{ trans('dict.list_long_lemmas') }}</a> 
            |
        @if (user_dict_add())
            <a href="{{route('lemma.create')}}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (user_dict_add())
            </a>
        @endif

        </p>
        @include('dict.lemma.search._lemma_form',['url' => '/dict/lemma/']) 

        @include('widgets.found_records', ['numAll'=>$numAll])

        @if ($numAll)
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
            @if (!$url_args['search_lang'])
                <th>{{ trans('dict.lang') }}</th>
            @endif
            @if (!$url_args['search_pos'])
                <th>{{ trans('dict.pos') }}</th>
            @endif
                <th>{{ trans('dict.interpretation') }}</th>
                <th>{{ trans('dict.wordforms') }}&nbsp;*</th>
                <th>{{ trans('messages.examples') }}&nbsp;**</th>
                @if (User::checkAccess('dict.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
            @foreach($lemmas as $lemma)
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.lemma') }}">
                    @if (empty($lemma->is_norm)) <sup>d</sup> @endif
                    <a href="{{ show_route($lemma, $args_by_get) }}">{{$lemma->lemma}}</a>
                    @if ($lemma->features && !empty($lemma->features->number))  
                    <sup>{{ $lemma->features->number }}</sup>
                    @endif
                </td>
            @if (!$url_args['search_lang'])
                <td data-th="{{ trans('dict.lang') }}">
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
            @endif
            @if (!$url_args['search_pos'])
                <td data-th="{{ trans('dict.pos') }}">
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                        {{$lemma->featsToString()}}
                    @endif
                </td>
            @endif
                <td data-th="{{ trans('dict.interpretation') }}">
                    @foreach ($lemma->getMultilangMeaningTexts() as $meaning_string) 
                        {{$meaning_string}}<br>
                    @endforeach
                </td>
                <td data-th="{{ trans('dict.wordforms') }}">                    
                    @if ($lemma->wordforms && $lemma->wordforms()->count())
                    {{$lemma->wordforms()->whereNotNull('gramset_id')->count()}}
                        @if ($lemma->wordforms()->whereNull('gramset_id')->count())
                        + <span class="unchecked-count">{{$lemma->wordforms()->whereNull('gramset_id')->count()}}</span>
                        @endif
                    @elseif (!$lemma->isChangable())
                    —
                    @else
                    <span class="unchecked-count">0</span>
                    @endif
                </td>
                <td data-th="{{ trans('messages.examples') }}">
                    <?php $total_ex = $lemma->countExamples();?>
                    @if ($total_ex)
                        <?php $unchecked = $lemma->countUncheckedExamples();?>
                        {{$lemma->countCheckedExamples()}} /
                        @if ($unchecked >0)
                            <span class="unchecked-count">
                        @endif
                        {{$unchecked}} 
                        @if ($unchecked >0)
                            </span>
                        @endif
                        /
                    @endif
                    {{$lemma->countExamples()}}
                </td>
                @if (User::checkAccess('dict.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => '/dict/lemma/'.$lemma->id.'/edit',
                             ])
                    @include('widgets.form.button._delete', 
                             ['is_button'=>true, 
                              'without_text' => true,
                              'route' => 'lemma.destroy', 
                              'args'=>['id' => $lemma->id]])
                </td>
                @endif
            </tr>
            @endforeach
        </table>
            {!! $lemmas->appends($url_args)->render() !!}
            
            <p><big>*</big> -  {{ trans('dict.wordform_comment') }}</p>
            <p><big>**</big> -  {{ trans('dict.example_comment') }}</p>
            <p><big><sup>d</sup></big> -  {{ trans('dict.is_norm_values')[0] }} {{ trans('dict.lemma') }}</p>
            <p><big><sup>1</sup></big> -  {{ trans('dict.numbers')[1] }}</p>
            <p><big><sup>2</sup></big> -  {{ trans('dict.numbers')[2] }}</p>
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
    {!!Html::script('js/search.js')!!}
    {!!Html::script('js/help.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    toggleSearchForm();
    recDelete('{{ trans('messages.confirm_delete') }}');
    selectWithLang('.select-dialects', "/dict/dialect/list", 'search_lang', '');
    selectConcept('search_concept_category', 'search_pos', '', true);
@stop


