@extends('layouts.page')

@section('page_title')
Исправление данных
@endsection

@section('body')
        <p><a href="/service/correct/add_wordform_affixes">Добавить аффиксы словоформам</a> (с грамсетами и неаналитические формы (без пробелов))</p>
        <p><a href="/service/correct/add_meaning_text_links">Добавить связи текст-значения</a></p>
        <p><a href="/service/correct/add_text_wordform_links">Добавить связи текст-словоформы</a></p>
        <p><a href="/service/correct/generate_wordforms">Сгенерировать словоформы по имеющимся</a></p>
        <p><a href="/service/correct/calculate_lemma_wordforms">Записать у лемм количество словоформ</a> ({{$count_lemmas_without_wordform_total}})</p>
        <p><a href="/service/correct/check_meaning_text">Исправить связи слово-значение (meaning_text)</a> 
           (исправить случаи, если есть проверенные и непроверенные примеры у одного слова, последним поставить метку "совсем не подходит")</p>
        <p><a href="/service/correct/add_accusatives">Добавить аккузативы для собственно карельских именных частей речи</a> (обходим все именные части речи, если есть номинативы и генитивы, создаем в этих диалектах соответствующие аккузативы)</p>
        <p><a href="/service/correct/add_approx_term">Добавить аппроксимативы и терминативы для ливвиковских именных частей речи</a></p>
        <p><a href="/service/correct/create_initial_wordforms">Создать минимальный набор словоформ</a>. Если у изменяемой леммы нет основ, создаем основу 0 и генерируем леммы.</p>
        <p><a href="/service/correct/check_parallel_texts">Проверить параллельные тексты</a>. Вывести ссылки на тексты с параллельным русским, если не совпадает количество предложений.</p>
        <p><a href="/service/correct/move_char_out_word">Перенос несловарного символа за пределы слова</a> (найти предложения, в которых есть слова, начинающиеся на несловарные символы и вынести эти символы влево за разметку слова)</p>
        <p><a href="/service/correct/add_audiofiles_to_db">Добавить аудио-файлы в БД</a>
        <p><a href="/service/correct/add_synonyms">Добавить синонимов из группы понятий</a>
        <p><a href="/service/correct/add_approx_term">Добавить аппроксимативы и терминативы для ливвиковских именных частей речи</a></p>
        <p><a href="/service/correct/add_src_for_concepts">Заполнить поля-источники у картинок понятий</a></p>
        <p><a href="/service/correct/extra_gramsets">Выявить у разметки лишние грамсеты</a></p>
        <p><a href="/service/correct/lemmas_u">Леммы с Ü</a></p>
        
{{--        <p><a href="/service/correct/tmp_fill_wordform_for_search">Заполнить wordform for search у таблицы связей "лемма"-"словоформа"</a>.</p>
        <p><a href="/service/correct/tmp_fill_genres">Добавить жанры для вепсских сказок</a>.</p>
        <p><a href="/service/correct/tmp_split_into_sentences">Разбить тексты на предложения</a>.</p>
        <p><a href="/service/correct/tmp_word_numbers_for_words">Пронумеровать слова в предложениях</a>.</p>
        <p><a href="/service/correct/tmp_fill_sentence_id_in_words">Добавить ссылки в таблице слов на предложения</a>.</p>
        <p><a href="/service/correct/tmp_fill_sentence_id_in_text_wordform">Добавить ссылки в таблице связей текст-словоформа на предложения</a>.</p>
        <p><a href="/service/correct/tmp_fill_word_id_in_text_wordform">Добавить ссылки в таблице связей текст-словоформа на слова</a>.</p> --}}
    
@endsection