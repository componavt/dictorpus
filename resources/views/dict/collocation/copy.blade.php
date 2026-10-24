@extends('layouts.page')

@section('page_title')
{{ trans('collocations.copy_title') }}
@stop

@section('headExtra')
    {!! Html::style('css/select2.min.css') !!}
@stop

@section('body')
    <p>
        <a href="{{ $editor_url  }}">
            {{ trans('collocations.title') }}
        </a>
     @if ($synset_url)
        | <a href="{{ $synset_url }}">{{ trans('collocations.back_to_synset', ['num' => $synset_id]) }}</a>
    @endif
   </p>

    <p>
        <strong>{{ trans('collocations.copy_target') }}:</strong>
        {{ $meaning->lemma->lemma }}<sup>{{ $meaning->meaning_n }}</sup>
        — {{ $meaning->getMeaningTextByLangCode('ru') }}
    </p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ $base_url.'/copy' }}">
        @include('dict.collocation._synset_context')

        <div class="form-group">
            <label for="source_meaning_id">
                {{ trans('collocations.copy_source') }}
            </label>

            <select id="source_meaning_id"
                    name="source_meaning_id"
                    class="form-control"
                    data-search-url="{{ $base_url.'/search' }}"
                    data-placeholder="{{ trans('collocations.search_placeholder') }}"
                    style="width: 100%"
                    required>
                <option value=""></option>

                @if ($source)
                    <option value="{{ $source->id }}" selected>
                        {{ $source->lemma->lemma }}
                        ({{ $source->meaning_n }}.
                        {{ $source->getMeaningTextByLangCode('ru') }})
                    </option>
                @endif
            </select>
        </div>

        <button type="submit" class="btn btn-default">
            {{ trans('collocations.copy_preview') }}
        </button>
    </form>

    @if ($source)
        <hr>

        @if (!count($copy_rows))
            <p>{{ trans('collocations.copy_empty_source') }}</p>
        @else
            <p>{{ trans('collocations.copy_description') }}</p>

            @if (!$copyable_count)
                <p>{{ trans('collocations.copy_no_available') }}</p>
            @endif

            <?php
            $has_old_selection = old('selection_submitted') === '1';

            $checked_ids = array_map(
                'intval',
                (array) old('collocation_ids', [])
            );
            ?>

            <form method="POST" action="{{ $base_url.'/copy' }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="source_meaning_id" value="{{ $source->id }}">
                <input type="hidden" name="selection_submitted" value="1">

                @include('dict.collocation._synset_context')

                <p>
                    <label>
                        <input type="checkbox" id="copy-select-all"
                               {{ !$copyable_count ? 'disabled' : '' }}>
                        {{ trans('collocations.copy_select_all') }}
                    </label>
                </p>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ trans('collocations.category') }}</th>
                            <th>{{ trans('collocations.partner') }}</th>
                            <th>{{ trans('collocations.display_gloss') }}</th>
                            <th>{{ trans('collocations.copy_status') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($copy_rows as $row)
                            <?php
                            $collocation = $row['collocation'];
                            $partner = $collocation->collocateMeaning;

                            $checked = !$row['exists']
                                && (!$has_old_selection
                                    || in_array(
                                        (int) $collocation->id,
                                        $checked_ids,
                                        true
                                    ));
                            ?>

                            <tr>
                                <td>
                                    @if (!$row['exists'])
                                        <input type="checkbox"
                                               name="collocation_ids[]"
                                               value="{{ $collocation->id }}"
                                               class="copy-item"
                                               {{ $checked ? 'checked' : '' }}>
                                    @endif
                                </td>

                                <td>
                                    {{ $collocation->conceptCategory
                                        ? $collocation->conceptCategory->name
                                        : trans('collocations.without_category') }}
                                </td>

                                <td>
                                    <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/'.$partner->lemma_id).'#meaning-'.$partner->id }}">{{ $partner->lemma->lemma }}</a><sup>{{ $partner->meaning_n }}</sup>
                                </td>

                                <td>{{ $collocation->display_gloss }}</td>

                                <td>
                                    @if ($row['conflict'])
                                        <span class="text-warning">
                                            {{ trans('collocations.copy_conflict') }}
                                        </span>
                                    @elseif ($row['exists'])
                                        {{ trans('collocations.copy_already_exists') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="submit"
                        class="btn btn-primary"
                        {{ !$copyable_count ? 'disabled' : '' }}>
                    {{ trans('collocations.copy') }}
                </button>

                <a href="{{ $editor_url  }}" class="btn btn-default">
                    {{ trans('collocations.cancel') }}
                </a>
            </form>
        @endif
    @endif
@stop

@section('footScriptExtra')
    {!! Html::script('js/select2.min.js') !!}
@stop

@section('jqueryFunc')
    var source_select = $('#source_meaning_id');

    source_select.select2({
        width: '100%',
        minimumInputLength: 2,
        placeholder: source_select.attr('data-placeholder'),
        ajax: {
            url: source_select.attr('data-search-url'),
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

    // Синхронизирует общий флажок с выбором отдельных записей.
    function updateCopySelectAll() {
        var items = $('.copy-item');
        var checked_count = items.filter(':checked').length;

        $('#copy-select-all')
            .prop('checked', items.length > 0 && checked_count === items.length)
            .prop('indeterminate', checked_count > 0 && checked_count < items.length);
    }

    $('#copy-select-all').on('change', function () {
        $('.copy-item').prop('checked', this.checked);
        updateCopySelectAll();
    });

    $('.copy-item').on('change', updateCopySelectAll);

    updateCopySelectAll();
@stop