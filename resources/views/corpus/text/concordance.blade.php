@extends('layouts.page')

@section('page_title')
{{ trans('navigation.texts') }}
@stop

@section('body')
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/').$args_by_get}}">{{ trans('messages.back_to_list') }}</a> |
            <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'/').$args_by_get }}">{{ trans('messages.back_to_show') }}</a>            
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$text->id.'/stats') }}">Вернуться к статистике</a>            
        </p>
        
        <h2>Конкорданс {{ trans('corpus.of_text') }} &laquo;{{ $text->title }}&raquo;</h2>
        
        <p>В конкорданс включены только проверенные и неразмеченные слова.
        @if ($unchecked_count)
            В тексте осталось {{ number_with_space($unchecked_count, 0, ',', ' ') }} {{ trans('corpus.of_unchecked_words', $unchecked_count%10==0 ? $unchecked_count : ($unchecked_count%100>20 ? $unchecked_count%10  : $unchecked_count%100)) }}.
            Чтобы включить их в конкорданс, закончите проверку.
        @endif
        </p>
        
        <table class='table-bordered'>
            <tr>
                <th>Часть речи</th>
                <th>ФОРМА</th>
                <th>Пример</th>
                <th>Исходное написание</th>
                <th>Начальная форма</th>
                <th>Перевод</th>
                <th>Количество употреблений</th>
            </tr>
        @foreach ($concordance as $pos=>$gramsets) 
<?php       ksort($gramsets); ?>
            @foreach ($gramsets as $gramset => $words) 
<?php           ksort($words); ?>
                @foreach ($words as $word => $cyrwords) 
<?php               ksort($cyrwords); ?>
                    @foreach ($cyrwords as $cyrword => $lemmas)
<?php                   ksort($lemmas); ?>
                        @foreach ($lemmas as $lemma => $meanings)
<?php                       ksort($meanings); ?>
                            @foreach ($meanings as $meaning => $count) 
            <tr>
                <td>{{ $pos }}</td>
                <td>{{ $gramset }}</td>
                <td>{{ $word }}</td>
                <td>{{ $cyrword }}</td>
                <td>{{ $lemma }}</td>
                <td>{{ $meaning }}</td>
                <td>{{ $count }}</td>
            </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            @endforeach
        @endforeach
        </table>
        
@stop
