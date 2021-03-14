<?php $list_count = $url_args['limit_num'] * ($url_args['page']-1) + 1;?>
@extends('layouts.page')

@section('page_title')
{{ trans('navigation.wordforms') }}
@stop

@section('headExtra')
    {!!Html::style('css/select2.min.css')!!}
    {!!Html::style('css/table.css')!!}
@stop

@section('body')
<div class="row">
    <div class="col-sm-6 col-lg-5">
        <p><a href="{{ LaravelLocalization::localizeURL('/dict/wordform/with_multiple_lemmas') }}">{{ trans('dict.wordforms_linked_many_lemmas') }}</a></p>
    </div>
    <div class="col-sm-6 col-lg-7">
        <p class="comment" style="text-align: right">{!!trans('messages.search_comment')!!}</p>
    </div>
</div>
        
        @include('dict.wordform._search_form') 

        @include('widgets.founded_records', ['numAll'=>$numAll])

        @if ($wordforms)
        <br>
        <table class="table-bordered table-wide table-striped rwd-table wide-lg">
        <thead>
            <tr>
                <th>No</th>
                <th>{{ trans('dict.wordform') }}</th>
                <th>{{ trans('dict.gram_attr') }}</th>
                <th>{{ trans('dict.lemmas') }}</th>
                <th>{{ trans('dict.pos') }}</th>
                <th>{{ trans('dict.lang') }}</th>
                <th>{{ trans('dict.dialect') }}</th>
                @if (User::checkAccess('dict.edit'))
                <th>{{ trans('messages.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($wordforms as $wordform)
            <?php 
            if ($wordform->lemma_id) {
                $lemma_obj = \App\Models\Dict\Lemma::find($wordform->lemma_id);
                $lemma = (!$lemma_obj || !isset($lemma_obj->lemma))
                       ? '' : $lemma_obj->lemma;
                $pos_name = (!$lemma_obj || !isset($lemma_obj->pos->name))
                          ? '' : $lemma_obj->pos->name;
                $lang_name = (!$lemma_obj || !isset($lemma_obj->lang->name))
                           ? '' : $lemma_obj->lang->name;
            }
            ?>
            <tr>
                <td data-th="No">{{ $list_count++ }}</td>
                <td data-th="{{ trans('dict.wordform') }}">{{$wordform->wordform}}</td>
                <td data-th="{{ trans('dict.gram_attr') }}">
                    <?php 
                    if($wordform->gramset_id) {
                        $gramset = \App\Models\Dict\Gramset::find($wordform->gramset_id);
                        if ($gramset) {
                            print $gramset->gramsetString();
                        }
                    } ?>
                </td>
                <td data-th="{{ trans('dict.lemmas') }}">
                    @if($wordform->lemma_id && $lemma) 
                    <a href="lemma/{{$wordform->lemma_id}}{{$args_by_get}}">{{$lemma}}</a>
                    @endif
                </td>
                <td data-th="{{ trans('dict.pos') }}">
                    {{$pos_name}}
                </td>
                <td data-th="{{ trans('dict.lang') }}">
                    {{$lang_name}}
                </td>
                <td data-th="{{ trans('dict.dialect') }}">
                    @if($wordform->dialect_id)
                        {{\App\Models\Dict\Dialect::find($wordform->dialect_id)->name}}
                    @endif
                </td>
                @if (User::checkAccess('dict.edit'))
                <td data-th="{{ trans('messages.actions') }}">
                    @include('widgets.form.button._edit', 
                             ['is_button'=>true, 
                              'route' => '/dict/wordform/'.$wordform->id.'/edit',
                             ])
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
        </table>
        @endif
        {!! $wordforms->appends($url_args)->render() !!}
@stop

@section('footScriptExtra')
    {!!Html::script('js/select2.min.js')!!}
    {!!Html::script('js/special_symbols.js')!!}
    {!!Html::script('js/list_change.js')!!}
@stop

@section('jqueryFunc')
    toggleSpecial();
    selectDialect('search_lang', '{{ trans('dict.select_dialect') }}', true);
    selectGramset('search_lang', 'search_pos', '{{ trans('dict.select_gramset') }}', true);
@stop

