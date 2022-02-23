@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Определение диалектной принадлежности</h2>

    <table class="table-bordered table-wide table-striped rwd-table wide-md">
        <tr>
            <th rowspan="2">Маркер</th>
            <th rowspan="2">Диалектный вариант</th>
    @foreach ($gr_dialects as $gr_name => $cols)
            <th colspan="{{$cols}}">{{$gr_name}}</th>
    @endforeach
        </tr>
        <tr>
    @foreach ($dialects as $dialect_id => $dialect_name)
            <th>
                <a href="{{ LaravelLocalization::localizeURL('/corpus/text/?search_dialect='.$dialect_id)}}">{{$dialect_name}}</a>
            </th>
    @endforeach
        </tr>
        
    @foreach ($dmarkers as $marker)
        @if (sizeof($marker->mvariants)) 
        <tr>
            <td rowspan="{{sizeof($marker->mvariants)}}" style="vertical-align: top">
                <b>{{ $marker->id }}. {{ $marker->name }}</b>
            </td>
            <?php $count=1; ?>
            @foreach ( $marker->mvariants as $variant )
                @if($count >1) 
        <tr>    
                @endif
            <td><b>{{ $variant->name }}</b></td>
                @foreach (array_keys($dialects) as $dialect_id)
            <td style="vertical-align: top">
                    @foreach ($variant->searchWords($dialect_id)->groupBy('word')->selectRaw('word, count(*) as count, text_id, w_id')->get() as $word)
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$word->text_id.'?search_wid='.$word->w_id)}}">{{$word->word}}</a>
                        @if ($word->count >1)
                    ({{$word->count}})
                        @endif
                        <br>
                    @endforeach
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