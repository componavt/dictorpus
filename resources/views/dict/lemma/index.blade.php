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
    <div class="col-sm-6 col-md-5 col-lg-4">
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/sorted_by_length') }}">{{ trans('dict.list_long_lemmas') }}</a> 
            |
        @if (User::checkAccess('dict.edit'))
            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/create') }}{{$args_by_get}}">
        @endif
            {{ trans('messages.create_new_f') }}
        @if (User::checkAccess('dict.edit'))
            </a>
        @endif

        </p>
    </div>
    <div class="col-sm-6 col-md-7 col-lg-8">
        <p class="comment" style="text-align: right">{!!trans('messages.search_comment')!!}</p>
    </div>
</div>
        @include('dict.lemma.search._lemma_form',['url' => '/dict/lemma/']) 

        <p>{{ trans('messages.founded_records', ['count'=>$numAll]) }}</p>

        @if ($numAll)
        <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.lemma') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.pos') }}</th>
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
                <td data-th="{{ trans('dict.lemma') }}"><a href="lemma/{{$lemma->id}}{{$args_by_get}}">{{$lemma->lemma}}</a></td>
                <td data-th="{{ trans('dict.lang') }}">
                    @if($lemma->lang)
                        {{$lemma->lang->name}}
                    @endif
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                    @if($lemma->pos)
                        {{$lemma->pos->name}}
                        @include('dict.lemma.show.features')
                    @endif
                </td>
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
                    @elseif (in_array($lemma->pos_id, $not_changeable_pos_list))
                    —
                    @else
                    0
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
        @endif
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/rec-delete-link.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    recDelete('{{ trans('messages.confirm_delete') }}');
    selectWithLang('.select-dialects', "/dict/dialect/list", 'search_lang', '{{ trans('dict.select_dialect') }}');
    selectConcept('search_concept_category', 'search_pos', '{{ trans('dict.select_concept') }}', true);
@stop


