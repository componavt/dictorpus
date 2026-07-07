@extends('layouts.master')

@section('title')
    Подбор похожих значений для переноса английских толкований
@endsection

@section('content')

<div class="container">

    <h2>Подбор похожих значений для переноса английских толкований</h2>

    <p>
        Строки сгруппированы по русскому толкованию. Внутри группы —
        целевое значение (без English) и все похожие значения-источники
        (с уже существующим English), из которых можно взять перевод.
    </p>

    @if (session('status'))
        <div class="alert alert-success" style="margin-bottom: 15px;">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 15px;">
            <strong>Ошибки:</strong>
            <ul style="margin-top: 10px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('correct.similar-meanings') }}" style="margin-bottom: 20px;">
        <div class="form-group">
            <label for="limit"><strong>Показать групп:</strong></label>
            <input
                type="number"
                name="limit"
                id="limit"
                value="{{ isset($limit) ? $limit : 50 }}"
                min="1"
                max="500"
                class="form-control"
                style="width: 120px; display: inline-block;"
            >
            <button type="submit" class="btn btn-default">Обновить</button>
        </div>
    </form>

    @if (empty($groups))
        <div class="alert alert-info">
            Кандидаты не найдены.
        </div>
    @else

        <form method="POST" action="{{ route('correct.similar-meanings-save') }}">
            {{ csrf_field() }}

            <div style="margin-bottom: 15px;">
                <button type="submit" class="btn btn-primary">Сохранить отмеченные строки</button>
            </div>

            @foreach ($groups as $group)

                <h4 style="margin-top: 25px;">
                    Русское толкование: «{{ $group['meaning_ru'] }}»
                </h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th style="width: 60px;">OK</th>

                                <th>Цель: lemma</th>
                                <th>Цель: meaning_n</th>
                                <th>Цель: meaning_ru</th>
                                <th style="min-width: 280px;">Подтверждённый / исправленный meaning_en</th>

                                <th>Источник: lemma</th>
                                <th>Источник: meaning_n</th>
                                <th>Источник: meaning_ru</th>
                                <th>Источник: meaning_en</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($group['targets'] as $targetId => $target)

                                <?php
                                    $sourcesCount = count($target['sources']);

                                    $oldApproved = old('rows.' . $targetId . '.approved');
                                    $oldMeaningEn = old('rows.' . $targetId . '.meaning_en');

                                    $approvedChecked = $oldApproved ? true : false;
                                    $meaningEnValue = $oldMeaningEn !== null
                                        ? $oldMeaningEn
                                        : $target['proposed_meaning_en'];
                                ?>

                                @foreach ($target['sources'] as $sIndex => $source)
                                    <tr>
                                        @if ($sIndex === 0)
                                            {{-- 
                                                Целевые ячейки (OK, лемма, meaning_n, meaning_ru, поле ввода)
                                                показываются один раз на весь target и растягиваются
                                                по высоте на все источники, чтобы было видно,
                                                что это одно значение.
                                            --}}
                                            <td rowspan="{{ $sourcesCount }}" style="vertical-align: middle;">
                                                <input type="hidden" name="rows[{{ $targetId }}][target_meaning_id]" value="{{ $target['target_meaning_id'] }}">
                                                <input type="checkbox" name="rows[{{ $targetId }}][approved]" value="1" {{ $approvedChecked ? 'checked' : '' }}>
                                            </td>

                                            <td rowspan="{{ $sourcesCount }}" style="vertical-align: middle;">
                                                <a href="{{ route('lemma.show', $target['target_lemma_id']) }}">{{ $target['target_lemma'] }}</a><br>
                                                <small>ID meaning: {{ $target['target_meaning_id'] }}</small>
                                            </td>

                                            <td rowspan="{{ $sourcesCount }}" style="vertical-align: middle;">
                                                {{ $target['target_meaning_n'] }}
                                            </td>

                                            <td rowspan="{{ $sourcesCount }}" style="vertical-align: middle;">
                                                {{ $target['target_meaning_ru'] }}
                                            </td>

                                            <td rowspan="{{ $sourcesCount }}" style="vertical-align: middle;">
                                                <input
                                                    type="text"
                                                    id="meaning_en_{{ $targetId }}"
                                                    name="rows[{{ $targetId }}][meaning_en]"
                                                    value="{{ $meaningEnValue }}"
                                                    class="form-control"
                                                    style="min-width: 260px;"
                                                >
                                            </td>
                                        @endif

                                        <td>
                                            <a href="{{ route('lemma.show', $source['source_lemma_id']) }}">{{ $source['source_lemma'] }}</a><br>
                                            <small>ID meaning: {{ $source['source_meaning_id'] }}</small>
                                        </td>

                                        <td>
                                            {{ $source['source_meaning_n'] }}
                                        </td>

                                        <td>
                                            {{ $source['source_meaning_ru'] }}
                                        </td>

                                        <td>
                                            <strong>{{ $source['source_meaning_en'] }}</strong>
                                            <br>
                                            <a href="javascript:void(0);"
                                               onclick="document.getElementById('meaning_en_{{ $targetId }}').value = {{ json_encode($source['source_meaning_en']) }};">
                                                Использовать этот перевод
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                            @endforeach
                        </tbody>
                    </table>
                </div>

            @endforeach

            <div style="margin-top: 15px;">
                <button type="submit" class="btn btn-primary">Сохранить отмеченные строки</button>
            </div>
        </form>
    @endif
</div>

@endsection