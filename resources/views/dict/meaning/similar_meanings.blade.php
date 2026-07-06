@extends('layouts.master')

@section('title')
    Подбор похожих значений для переноса английских толкований
@endsection

@section('content')

<div class="container">

    <h2>Подбор похожих значений для переноса английских толкований</h2>

    <p>
        На этой странице показываются пары:
        слева — meaning с уже существующим English-переводом,
        справа — meaning без English-перевода.
        Эксперт может подтвердить перенос, отредактировать English вручную
        и сохранить только отмеченные строки.
    </p>

    {{-- Сообщение об успехе --}}
    @if (session('status'))
        <div class="alert alert-success" style="margin-bottom: 15px;">
            {{ session('status') }}
        </div>
    @endif

    {{-- Сообщение об ошибке --}}
    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Ошибки валидации --}}
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

    {{-- Небольшая форма для выбора лимита --}}
    <form method="GET" action="{{ route('correct.similar-meanings') }}" style="margin-bottom: 20px;">
        <div class="form-group">
            <label for="limit"><strong>Показать строк:</strong></label>
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

    @if (empty($candidates))
        <div class="alert alert-info">
            Кандидаты не найдены.
        </div>
    @else

        <form method="POST" action="{{ route('correct.similar-meanings-save') }}">
            {{ csrf_field() }}

            <div style="margin-bottom: 15px;">
                <button type="submit" class="btn btn-primary">Сохранить отмеченные строки</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th style="width: 60px;">OK</th>

                            <th>Источник: lemma</th>
                            <th>Источник: meaning_n</th>
                            <th>Источник: meaning_ru</th>
                            <th>Источник: meaning_en</th>

                            <th>Цель: lemma</th>
                            <th>Цель: meaning_n</th>
                            <th>Цель: meaning_ru</th>

                            <th style="min-width: 280px;">Подтверждённый / исправленный meaning_en</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $i => $row)

                            {{-- 
                                Для старого Laravel safer pattern:
                                если была ошибка валидации и redirect back withInput(),
                                пробуем восстановить old() значения вручную.
                            --}}
                            <?php
                                $oldApproved = old('rows.' . $i . '.approved');
                                $oldMeaningEn = old('rows.' . $i . '.meaning_en');

                                $approvedChecked = $oldApproved ? true : false;
                                $meaningEnValue = $oldMeaningEn !== null
                                    ? $oldMeaningEn
                                    : $row['proposed_meaning_en'];
                            ?>

                            <tr>
                                <td>
                                    <input type="hidden" name="rows[{{ $i }}][target_meaning_id]" value="{{ $row['target_meaning_id'] }}">
                                    <input type="checkbox" name="rows[{{ $i }}][approved]" value="1" {{ $approvedChecked ? 'checked' : '' }}>
                                </td>

                                <td>
                                    <a href="{{ route('lemma.show', $row['source_lemma_id']) }}">{{ $row['source_lemma'] }}</a><br>
                                    <small>ID meaning: {{ $row['source_meaning_id'] }}</small><br>
                                </td>

                                <td>
                                    {{ $row['source_meaning_n'] }}
                                </td>

                                <td>
                                    {{ $row['source_meaning_ru'] }}
                                </td>

                                <td>
                                    <strong>{{ $row['source_meaning_en'] }}</strong>
                                </td>

                                <td>
                                    <a href="{{ route('lemma.show', $row['target_lemma_id']) }}">{{ $row['target_lemma'] }}</a><br>
                                    <small>ID meaning: {{ $row['target_meaning_id'] }}</small><br>
                                </td>

                                <td>
                                    {{ $row['target_meaning_n'] }}
                                </td>

                                <td>
                                    {{ $row['target_meaning_ru'] }}
                                </td>

                                <td>
                                    <input
                                        type="text"
                                        name="rows[{{ $i }}][meaning_en]"
                                        value="{{ $meaningEnValue }}"
                                        class="form-control"
                                        style="min-width: 260px;"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 15px;">
                <button type="submit" class="btn btn-primary">Сохранить отмеченные строки</button>
            </div>
        </form>
    @endif
</div>

@endsection