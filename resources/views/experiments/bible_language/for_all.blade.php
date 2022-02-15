@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Анализ языковых конструкций {{ $for_selection ? 'отобранных' : '' }} {{ $lang_id == 4 ? 'собственно карельских' : '' }} текстов</h2>
    <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <tr>
            <th>&nbsp;</th>
        @foreach(array_values($corpuses) as $title)
            <th>{{$title}}</th>
        @endforeach
        </tr>
        
        @include('experiments.bible_language.for_all_row', ['title' => 'Общее количество текстов', 'totals' => $stats['text_total']])
        
        @include('experiments.bible_language.for_all_row', ['title' => 'Общее количество слов', 'totals' => $stats['word_total']])
        
        @include('experiments.bible_language.for_all_row', ['title' => 'Среднее количество слов в тексте', 'totals' => $stats['words_to_texts']])
        
        @include('experiments.bible_language.for_all_row', ['title' => 'Общее количество размеченных слов', 'totals' => $stats['linked_words']])
        
        @include('experiments.bible_language.for_all_row', ['title' => 'Доля размеченных слов', 'totals' => $stats['linked_words_to_all']])

        @include('experiments.bible_language.for_all_3row', ['var' => 'inf3', 'title1' => 'Количество инфинитивов III', 'title2' => 'Доля инфинитивов III'])        
        
        @include('experiments.bible_language.for_all_3row', ['var' => 'pot', 'title1' => 'Количество потенциалов', 'title2' => 'Доля потенциалов'])        
        
        @include('experiments.bible_language.for_all_3row', ['var' => 'cond', 'title1' => 'Количество кондиционалов', 'title2' => 'Доля кондиционалов'])        
        
    @if ($lang_id)
        @include('experiments.bible_language.for_all_3row', ['var' => 'ta', 'title1' => "Союз 'ta'", 'title2' => "Доля 'ta'"])        
        
        @include('experiments.bible_language.for_all_3row', ['var' => 'a', 'title1' => "Союз 'a'", 'title2' => "Доля 'a'"])        
        
        @include('experiments.bible_language.for_all_3row', ['var' => 'ni', 'title1' => "Частица 'ni'", 'title2' => "Доля 'ni'"])        
        
        @include('experiments.bible_language.for_all_3row', ['var' => 'no', 'title1' => "Междометье 'no'", 'title2' => "Доля 'no'"])        
        
        @include('experiments.bible_language.for_all_3row', ['var' => 'voi', 'title1' => "Междометье 'voi'", 'title2' => "Доля 'voi'"])        
    @endif
    </table>
    
@endsection
