@extends('layouts.page')

@section('page_title')
{{ trans('navigation.service') }}
@endsection

@section('body')
    <h2>Проверить в текстах неразмеченные слова и создать новые связи</h2>
        
    <p>Устанавить в БД words.checked=1. Количество слов в текстах без связи со словарем:</p>
    <ul>
    @foreach ($langs as $l_id=>$l_info)
        <li>
            <a href="/service/add_unmarked_links?search_lang={{$l_id}}">
                {{$l_info['name']}}
            </a> 
            ({{$l_info['unmarked_words_count']}})</li>
    @endforeach
    </ul>
@endsection