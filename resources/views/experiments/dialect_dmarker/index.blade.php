@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Определение диалектной принадлежности</h2>
    
    <p>Частоты считаются как отношение количества слов, удовлетворяющих критерию, к общему числу слов.</p>
    <p>Красным выделены результаты, не совпавшие с экспертной оценкой. 
       Если красная частота больше нуля, значит, предполагалось, что в этом диалекте не должно быть таких слов. 
       Возможно у текста неправильно определен диалект или в этой деревне пограничный диалект.
       Если красная частота нулевая, значит предполагалось, что такие слова должны быть.
       Выборка очень маленькая, результат мало о чем говорит.
    </p>
    <p>Есть смысл проверить экспертам ненулевые красные частоты. 
       Чтобы проверить результат, щелкните на частоту, откроется лексико-грамматический поиск.</p>
    <p>Маркеры с вариантами отсутствия не обсчитывались, так как особого смысла не имеют.</p>

    <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <tr>
            <th rowspan="2">Маркер</th>
            <th rowspan="2">Диалектный вариант</th>
    @foreach ($gr_dialects as $gr_name => $cols)
            <th colspan="{{$cols}}">{{$gr_name}}</th>
    @endforeach
        </tr>
        <tr>
    @foreach ($dialects as $dialect_id => $info)
            <th>{{$info['name']}}</th>
    @endforeach
        </tr>
        
        <tr>
            <td colspan='2'><b>Общее количество текстов</b></td>
    @foreach ($dialects as $dialect_id => $info)
            <td style="text-align: right">{{$info['text_total']}}</td>
    @endforeach
        </tr>
        
        <tr>
            <td colspan='2'><b>Общее количество слов</b></td>
    @foreach ($dialects as $dialect_id => $info)
            <td style="text-align: right">{{$info['word_total']}}</td>
    @endforeach
        </tr>
        
    @foreach ($dmarkers as $marker)
        @if (sizeof($marker->mvariants)) 
        <tr>
            <td rowspan="{{sizeof($marker->mvariants)}}">
                <b>{{ $marker->id }}. {{ $marker->name }}</b>
            </td>
            <?php $count=1; ?>
            @foreach ( $marker->mvariants as $variant )
                @if($count >1) 
        <tr>    
                @endif
            <td><b>{{ $variant->name }}</b></td>
                @foreach (array_keys($dialects) as $dialect_id)
            <td style="text-align: right">
                <a href='/ru/corpus/sentence/results?search_dialect[]={{$dialect_id}}&search_words[1][w]="{{$variant->template}}"'
                   style="text-decoration: none; color: {{$variant->rightFrequency($dialect_id) ? 'black' : 'red'}}">
                {{ $variant->frequency($dialect_id) }}
                </a>
            </td>
                @endforeach
                @if ( $count < sizeof($marker->mvariants) ) 
        </tr>    
                @endif
        @endforeach
        </tr>
        @endif
    @endforeach
    </table>
@endsection