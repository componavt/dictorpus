@extends('layouts.page')

@section('page_title')
{{ trans('collocations.title') }}: {{ $meaning->lemma->lemma }}
@stop

@section('headExtra')
    {!! Html::style('css/select2.min.css') !!}
@stop

@section('body')
    @if (session('matrix_notice'))
    <div class="alert alert-info">
        {{ session('matrix_notice') }}
    </div>
    @endif
    <p>
        <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$meaning->lemma_id) }}">
            {{ trans('collocations.back_to_article') }}
        </a>
    @if ($synset_url)
        | <a href="{{ $synset_url }}">{{ trans('collocations.back_to_synset', ['num' => $synset_id]) }}</a>
    @endif
    @if (!empty($examples_return))
        | <a href="{{ $examples_return['url'] }}">{{ trans('meaning_examples.back_to_examples') }}</a>
    @endif
        | <a href="{{ $copy_url }}">{{ trans('collocations.copy_open') }}</a>
        | <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$meaning->lemma_id.'/history') }}">
            {{ trans('collocations.history') }}
        </a>
    </p>

    <h3>
        {{ trans('collocations.meaning', ['num' => $meaning->meaning_n]) }}
    </h3>

    <p>{{ $meaning->getMultilangMeaningTextsStringLocale() }}</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h3>
        {{ trans($editing ? 'collocations.edit' : 'collocations.add') }}
    </h3>

    <form method="POST" action="{{ $editing ? $base_url.'/'.$editing->id : $base_url }}">

        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        @include('dict.collocation._synset_context')

        @if ($editing)
            <input type="hidden" name="_method" value="PUT">
        @endif

        <div class="form-group">
            <label for="collocate_meaning_id">
                {{ trans('collocations.partner') }}
            </label>

            <select id="collocate_meaning_id" name="collocate_meaning_id"
                    class="form-control" data-search-url="{{ $base_url.'/search' }}"
                    data-placeholder="{{ trans('collocations.search_placeholder') }}"
                    style="width: 100%">
                <option value=""></option>

                @if ($selected_meaning)
                    <option value="{{ $selected_meaning->id }}"
                            data-display-gloss="{{ $selected_gloss }}" selected>
                        {{ $selected_meaning->lemma->lemma }}
                        ({{ $selected_meaning->meaning_n }}. {{ $selected_gloss }})
                    </option>
                @endif
            </select>
        </div>

        <div class="form-group">
            <label for="concept_category_id">
                {{ trans('collocations.category') }}
            </label>

            <select id="concept_category_id" name="concept_category_id" class="form-control">
                <option value="">{{ trans('collocations.without_category') }}</option>

                @foreach ($category_values as $category_id => $category_name)
                    <option value="{{ $category_id }}"
                        {{ (string) old('concept_category_id', $default_category_id) === (string) $category_id ? 'selected' : '' }}>
                        {{ $category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="display_gloss">
                {{ trans('collocations.display_gloss') }}
            </label>

            <textarea id="display_gloss" name="display_gloss" class="form-control"
                      rows="2">{{ old('display_gloss', $default_display_gloss) }}</textarea>

            <button type="button" id="use-dictionary-gloss" class="btn btn-link">
                {{ trans('collocations.use_dictionary_gloss') }}
            </button>
        </div>

        <button type="submit" class="btn btn-primary">
            {{ trans('collocations.save') }}
        </button>

        @if ($editing)
            <a href="{{ $editor_url }}" class="btn btn-default">
                {{ trans('collocations.cancel') }}
            </a>
        @endif
    </form>

    <hr>

    <h3>{{ trans('collocations.list') }}</h3>

    @if ($collocations->isEmpty())
        <p>{{ trans('collocations.empty') }}</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ trans('collocations.category') }}</th>
                    <th>{{ trans('collocations.partner') }}</th>
                    <th>{{ trans('collocations.display_gloss') }}</th>
                    <th>{{ trans('collocations.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($collocations as $collocation)
                    <tr id="collocation-{{ $collocation->id }}">
                        <td>
                            {{ $collocation->conceptCategory ? $collocation->conceptCategory->name : trans('collocations.without_category') }}

                            @if (!$editing)
                                <button type="button" class="btn btn-link btn-xs choose-collocation-category"
                                        data-category-id="{{ $collocation->concept_category_id ?? '' }}"
                                        title="{{ trans('collocations.use_category') }}"
                                        aria-label="{{ trans('collocations.use_category') }}">
                                    <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                </button>
                            @endif
                        </td>

                        <td>
                            <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$collocation->collocateMeaning->lemma_id) }}">
                                {{ $collocation->collocateMeaning->lemma->lemma }}
                            </a><sup>{{ $collocation->collocateMeaning->meaning_n }}</sup>
                        </td>

                        <td>{{ $collocation->display_gloss }}</td>

                        <td style="white-space: nowrap">
                            <a href="{{ $base_url.'?'.http_build_query(array_merge($navigation_args, ['edit' => $collocation->id])) }}" 
                                class="btn btn-default btn-sm btn-warning"
                            title="{{ trans('collocations.edit') }}"
                            aria-label="{{ trans('collocations.edit') }}">
                                <i class="fa fa-pencil-alt fa-lg" aria-hidden="true"></i>
                            </a>

                            <form method="POST" action="{{ $base_url.'/'.$collocation->id }}" class="collocation-delete"
                                data-confirm="{{ trans('collocations.confirm_delete') }}"
                                style="display: inline-block; margin: 0">

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">

                                @include('dict.collocation._synset_context')

                                <button type="submit" class="btn btn-danger btn-sm" title="{{ trans('collocations.delete') }}"
                                        aria-label="{{ trans('collocations.delete') }}">
                                    <i class="fa fa-trash fa-lg" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@stop

@section('footScriptExtra')
    {!! Html::script('js/select2.min.js') !!}
@stop

@section('jqueryFunc')
    var partner_select = $('#collocate_meaning_id');

    var dictionary_gloss = partner_select
        .find('option:selected')
        .attr('data-display-gloss') || '';

    partner_select.select2({
        width: '100%',
        minimumInputLength: 2,
        placeholder: partner_select.attr('data-placeholder'),
        ajax: {
            url: partner_select.attr('data-search-url'),
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return data;
            }
        }
    });

    partner_select.on('select2:select', function (event) {
        dictionary_gloss = event.params.data.display_gloss || '';
        $('#display_gloss').val(dictionary_gloss);
    });

    $('#use-dictionary-gloss').on('click', function () {
        $('#display_gloss').val(dictionary_gloss);
    });

    $('.collocation-delete').on('submit', function () {
        return window.confirm($(this).attr('data-confirm'));
    });

    // Подставляет категорию строки в форму добавления без сохранения данных.
    $('.choose-collocation-category').on('click', function () {
        var category_id = $(this).attr('data-category-id');

        $('#concept_category_id')
            .val(category_id)
            .trigger('change')
            .focus();
    });
@stop