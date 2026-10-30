@extends('layouts.page')

@section('page_title')
    Корпусные кандидаты сочетаемости
@stop

@section('headExtra')
    {!! css('select2.min') !!}
    {!! css('meaning-example-form') !!}
    {!! css('synset-candidates') !!}
@stop

@section('body')
    <p><a href="{{ $back_url }}">← К редактору синсета</a></p>

    <h2>Корпусные кандидаты: {{ $synset->name }}</h2>

    <p>Участников: {{ $members->count() }} из {{ $all_members->count() }}.
        Найдено значений-партнёров: {{ $partner_total }}.
        Показано: {{ $partner_count }}.</p>

    @include('dict.meaning.examples.form._form', [
        'candidate_mode' => true,
        'matrix_mode' => true,
        'form_url' => $base_url,
        'form_values' => $filters,
    ])

    <hr>

    <p class="text-muted">
        Число — количество разных предложений.
        ✓ — пара уже внесена в сочетаемость.
        Прочерк — находок при этих условиях нет.
        Проверенность в ячейке относится к значению партнёра.
    </p>

    @if (!$all_members->count())
    <p>В синсете нет участников.</p>
    @elseif (!$members->count())
    <p>{{ trans('collocation_candidates.matrix_members_empty') }}</p>
    @elseif (!$partner_total)
    <p>При текущих условиях кандидаты не найдены.</p>
    @elseif (!$partner_count)
    <p>{{ trans('collocation_candidates.matrix_rows_empty') }}</p>
    @else
    <div class="synset-candidates-scroll">
        <table class="table table-bordered table-hover synset-candidates">
            <thead>
                <tr>
                    <th>Значение партнёра</th>

        @foreach ($members as $member)
                    <th class="candidate-member">
                        <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/' . $member->lemma_id) }}#meaning-{{ $member->id }}">
                            {{ $member->lemma->lemma }}</a><sup>{{ $member->meaning_n }}</sup>
                        
                        <div class="candidate-gloss">
                            {{ $member->getMeaningTextByLangCode('ru') }}
                        </div>

                        <div class="candidate-member-type">
                            {{ in_array($member->id, $core_ids) ? 'ядро' : 'периферия' }}
                        </div>
                    </th>
        @endforeach
                </tr>
            </thead>

            <tbody>
        @foreach ($candidate_groups as $group)
            @foreach ($group['items'] as $item)
                <tr>
                    <td class="candidate-partner">
                        <a href="{{ LaravelLocalization::localizeURL('/dict/lemma/' . $group['lemma_id']) }}#meaning-{{ $item['meaning_id'] }}">{{ $group['lemma'] }}</a><sup>{{ $item['meaning_n'] }}</sup>            

                        <div class="candidate-gloss">{{ $item['gloss'] }}</div>
                    </td>

                @foreach ($members as $member)
                    <?php
                        $cell = $item['cells'][$member->id] ?? null;
                    ?>

                    <td class="candidate-cell{{ $cell && $cell['recorded'] ? ' candidate-cell-recorded' : '' }}">
                    @if ($cell)
                        <a href="{{ $cell['examples_url'] }}" target="_blank" rel="noopener" title="Открыть найденные предложения">{{ $cell['sentences'] }}</a>

                        @if ($cell['recorded'])
                        <span class="candidate-recorded-mark" title="Пара уже внесена в сочетаемость">✓</span>
                        @endif

                        <div class="candidate-counts">
                            <span title="Предложения с проверенным значением партнёра">пр.: {{ $cell['checked_sentences'] }}</span>
                            <span title="Предложения с непроверенным значением партнёра">непр.: {{ $cell['unchecked_sentences'] }}</span>
                        </div>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                    </td>
                @endforeach
                </tr>
            @endforeach
        @endforeach
            </tbody>
        </table>
    </div>
    @endif
@stop

@section('footScriptExtra')
    {!! js('select2.min') !!}
    {!! js('meaning_example_filters') !!}
@stop

@section('jqueryFunc')
    initMeaningExampleFilters();

    function updateMatrixRowOptions() {
        var selected_ids = $('#candidate-matrix-members').val() || [];

        $('#candidate-matrix-row-options').toggle(selected_ids.length >= 2);
    }

    $('#candidate-matrix-members').on('change', updateMatrixRowOptions);
    updateMatrixRowOptions();
@stop