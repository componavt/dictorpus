@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Генерация словоформ для людиковского наречия карельского языка на примере Святозерского диалекта</h2>
    <h3>
    @if ($what == 'verbs')
        Глаголы
    @else 
        Именные части речи
    @endif
    </h3>

    
    <table class='table-bordered table-wide table-striped rwd-table wide-md'>
        <tr>
            <th></th>
    @foreach ($cols as $col)    
            <th>{{ $col }}</th>
    @endforeach
        </tr>
        
    @foreach ($gramsets as $category_name => $category_gramsets)
        <tr><th colspan="2">{{$category_name}}</th></tr>
    
        @foreach ($category_gramsets as $gramset_id => $gramset_name)
        <tr>
            <td>{{ $gramset_name }}</td>
            @foreach ($cols as $col)    
            <td>{!! join('<br>', $affixes[$gramset_id][$col]) !!}</td>
            @endforeach
        </tr>
        @endforeach
    @endforeach
    </table>
    
@endsection

@section('footScriptExtra')
    {!!Html::script('js/text.js')!!}
    {!!Html::script('js/form.js')!!}
@endsection

@section('jqueryFunc')
    limitTextarea("#text");
@endsection