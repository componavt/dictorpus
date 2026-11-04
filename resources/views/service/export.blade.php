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

    <hr>
    <h3>Экспорт для dictorpus-data</h3>
    <p>
        Все словарные и корпусные CSV для четырёх языковых разновидностей,
        а также ливвиковские синонимические ряды.
        Файлы сразу сжимаются в Zstandard.
        Документация, samples и semantics не создаются и не изменяются.
    </p>

    <form method="POST" action="{{ \LaravelLocalization::localizeURL('/service/export/dictorpus-data') }}">
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