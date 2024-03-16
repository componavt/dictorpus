    <table class='table-bordered table-wide table-striped rwd-table wide-md'>
        <tr>
            <td colspan="2"></td>
            <td colspan="2">Основа 1 (слабая гласная основа презенс)</td>
            <td>Основа 2 (вспом. сильн. гл.)</td>
            <td>Основа 3 (слабая гласная основа имперфекта)</td>
            <td>Основа 4 (сильная гласная основа имперфекта)</td>
            <td>Основа 5 (сильная согласная основа)</td>
            <td>Основа 6 (слабая основа пассива)</td>
            <td>Основа 7 (сильная основа пассива)</td>
        </tr>
        
        @php $count = 1; @endphp
        
        @foreach ($bases as $lemma_id => $lemma_bases)
        <tr>
            <td>{{ $count++ }}</td>
            <td><a href="{{ route('lemma.show',$lemma_id) }}">{{ $lemma_bases[0] }}</a></td>
            <td>{{ $lemma_bases[1] }}</td>
            <td>{!! str_diff($lemma_bases[0], $lemma_bases[1]) !!}</td>
            @for ($i=2; $i<6; $i++)
            <td>{{ $lemma_bases[$i] }}</td>
{{--                @if ($i>3)
            <td>{!! str_diff($lemma_bases[$i-3], $lemma_bases[$i]) !!}</td>                
                @endif--}}
            @endfor
        </tr>
        @endforeach
    </table>
