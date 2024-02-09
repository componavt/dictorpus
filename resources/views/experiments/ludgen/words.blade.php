@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Генерация словоформ для людиковского наречия карельского языка на примере Святозерского диалекта</h2>
    <h3>
        Отобранные слова<br>    
    @if ($what == 'verbs')
        Глаголы
    @else 
        Именные формы
    @endif
    </h3>

    @foreach ($gramsets as $category_name => $category_gramsets)
    <h4>{{$category_name}}</h4>
    
    <table class='table-bordered table-wide table-striped rwd-table wide-md'>
        <tr>
            <td colspan="3"></td>
        @foreach ($category_gramsets as $gramset_id => $gramset_name)
            <td>{{ $gramset_name }}</td>
        @endforeach
        </tr>
        
        @php $count = 1; @endphp
        
        @foreach ($lemmas as $lemma)
        <tr>
            <td>{{ $count++ }}</td>
            <td><a href="{{ route('lemma.show',$lemma->id) }}">{{ $lemma->lemma}}</a></td>
            <td style="text-align: right">{{ $lemma->wordforms()->wherePivot('dialect_id',$dialect_id)->count() }}</td>
            
            @foreach ($category_gramsets as $gramset_id => $gramset_name)
            <td>{{ $lemma->wordform($gramset_id, $dialect_id) }}</td>
            @endforeach
        </tr>
        @endforeach
    </table>
    @endforeach
    
{{--   @include('experiments.ludgen._words_verbs', ['lemmas' => $lemmas['verbs']]) --}}
    
@endsection

@section('footScriptExtra')
    {!!Html::script('js/text.js')!!}
    {!!Html::script('js/form.js')!!}
@endsection

@section('jqueryFunc')
    limitTextarea("#text");
@endsection