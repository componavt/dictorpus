@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Анализ языковых конструкций библейских текстов</h2>
    <h3>Исследуемые тексты</h3>
    <ol>
    @foreach ($texts as $text)
    <li><a href="/ru/corpus/text/{{$text->id}}">{{$text->title}}</a></li>
    @endforeach
    </ol>
    
    <h3>Формы</h3>
    <table>
        <tr style="vertical-align: top">
        @foreach($grams as $gram)
            <td>
                <b>{{$gram->name_ru}}</b>
            <ol>
            @foreach ($words[$gram->id] as $word)
            <li><a href="/ru/corpus/text/{{$text->id}}?search_wid={{$word->w_id}}">{{$word->word}}</a></li>
            @endforeach
            </ol>
            </td>
        @endforeach
        </tr>
    </table>
    
    <h3>Союз 'ta'</h3>
    @foreach($ta_positions as $i => $title)
        @if(isset($tap_words[$i])) 
            <b>{{$title}}</b>
            <ol>
            @foreach($tap_words[$i] as $word)
                <li>{!! $word->getClearSentence(true) !!}</li>
            @endforeach
            </ol>
        @endif
    @endforeach
@endsection