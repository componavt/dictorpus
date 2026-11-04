@extends('layouts.page')

@section('page_title')
Экспорт
@endsection

@section('body')
    <p><a href="export/unimorph">Экспорт словоформ в Unimorph</a>    </p>
    <ul>
    @foreach ($langs as $l_id=>$l_name)
        <li><a href="export/unimorph?search_lang={{$l_id}}">{{$l_name}}</a></li>
    @endforeach
    </ul>

    <p><a href="export/compounds_for_unimorph">Экспорт фразеологизмов в Unimorph</a></p>
    <p><a href="export/bible">Экспорт вепсской Библии</a></p>
    <p><a href="export/for_mobile">Экспорт данных для мобильной версии</a></p>
    <p><a href="export/for_speech">Экспорт ливвиковских текстов и словоформ для распознавания речи</a></p>
    <p><a href="export/multidict">Экспорт лемм мультисловаря</a></p>
    <p><a href="export/concepts">Экспорт всех понятий и всех категорий понятий</a></p>
    <p><a href="export/concepts_without_images">Экспорт понятий без картинок</a></p>
    <p><a href="export/multidict_without_concepts">Экспорт лемм мультисловаря без понятий</a></p>
    <p><a href="export/olo_dict">Экспорт ливвиковского словаря</a></p>
    <p><a href="export/for_yandex">Экспорт предложений для Яндекса</a></p>
    <p><a href="export/russian_meanings">Экспорт русских толкований из словаря для WordNet</a></p>
    <p><a href="export/russian_translations">Экспорт русских переводов примеров к словарю для WordNet</a></p>
    <p><a href="export/for_multimedia_dictionary">Экспорт лемм для Мультимедийного словаря карельского языка</a></p>
    <p><a href="export/for_morph-disambig">Экспорт данных для задач разрешения морфологической неоднозначности (morph-disambig)</a></p>
    
    <hr>
    <h3>Синонимические ряды и сочетаемость</h3>

    <p>
        Ливвиковские прилагательные: все сохранённые синсеты,
        включая черновики, их состав, возможные кандидаты,
        сочетаемость и прикреплённые корпусные примеры.
    </p>

    <form method="POST"
        action="{{ \LaravelLocalization::localizeURL('/service/export/synonyms') }}">
        {!! csrf_field() !!}

        <button type="submit" class="btn btn-primary">
            Выгрузить CSV
        </button>
    </form>

    @if (session('synonym_export_error'))
        <div class="alert alert-danger" style="margin-top: 15px;">
            <p>Экспорт не завершён:</p>
            <p>{{ session('synonym_export_error') }}</p>
        </div>
    @endif

    @if (session('synonym_export_result'))
        <?php $synonym_result = session('synonym_export_result'); ?>

        <div class="alert alert-success" style="margin-top: 15px;">
            Экспорт завершён. Ниже доступны новые файлы.
        </div>

        <h4>Количество строк</h4>

        <ul>
            @foreach ($synonym_result['counts'] as $name => $count)
                <li>{{ $name }}: {{ $count }}</li>
            @endforeach
        </ul>

        <h4>Проверка корпусных примеров</h4>

        <ul>
            <li>
                Не найдено однозначное соответствие предложению:
                {{ $synonym_result['warnings']['missing_sentence'] }}
            </li>
            <li>
                Отсутствует русский перевод:
                {{ $synonym_result['warnings']['missing_translation'] }}
            </li>
            <li>
                Текущий XML отличается от сохранённого:
                {{ $synonym_result['warnings']['changed_xml'] }}
            </li>
        </ul>

        <p>
            В CSV сохраняется исходный XML прикреплённого примера.
            Примеры без найденного предложения или перевода не удаляются.
        </p>
    @endif

    @if (!empty($synonym_files))
        <h4>Последняя успешная выгрузка</h4>

        <ul>
            @foreach ($synonym_files as $synonym_file)
                <li>
                    <a href="{{ $synonym_file['url'] }}" download>
                        {{ $synonym_file['name'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <hr>

    <h3>Экспорт для dictorpus-data</h3>

    <p>
        Все словарные и корпусные CSV для четырёх языковых разновидностей,
        а также ливвиковские синонимические ряды.
        Файлы сразу сжимаются в Zstandard.
        Документация, samples и semantics не создаются и не изменяются.
    </p>

    <form method="POST"
        action="{{ \LaravelLocalization::localizeURL('/service/export/dictorpus-data') }}">
        {!! csrf_field() !!}

        <button type="submit" class="btn btn-primary">
            Выгрузить dictorpus-data
        </button>
    </form>

    @if (session('dictorpus_data_error'))
        <div class="alert alert-danger" style="margin-top: 15px;">
            {{ session('dictorpus_data_error') }}
        </div>
    @endif

    @if (session('dictorpus_data_result'))
        <?php $data_result = session('dictorpus_data_result'); ?>

        <div class="alert alert-success" style="margin-top: 15px;">
            Экспорт завершён.
            Создано файлов: {{ $data_result['file_count'] }}.
        </div>

        <p>Диагностика прикреплённых примеров сочетаний:</p>

        <ul>
            <li>
                Не найдено предложение:
                {{ $data_result['synonyms']['warnings']['missing_sentence'] }}
            </li>
            <li>
                Нет русского перевода:
                {{ $data_result['synonyms']['warnings']['missing_translation'] }}
            </li>
            <li>
                Текущий XML отличается от сохранённого:
                {{ $data_result['synonyms']['warnings']['changed_xml'] }}
            </li>
        </ul>
    @endif

    @if (!empty($dictorpus_data_files))
        <h4>Последняя успешная выгрузка dictorpus-data</h4>

        <ul>
            @foreach ($dictorpus_data_files as $file)
                <li>
                    <a href="{{ $file['url'] }}" download>
                        {{ $file['name'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
    @endsection