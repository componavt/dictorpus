    <table class='table-bordered table-wide table-striped rwd-table wide-md'>
        <tr>
            <td colspan="2"></td>
            <td colspan="2">Основа 1 (слабая гласная основа)</td>
            <td>Основа 2 (основа иллатива)</td>
            <td>Основа 3 (партитив)</td>
            <td colspan="2">Основа 4 (вспом. осн. мн.., сл.)</td>
            <td colspan="2">Основа 5 (вспом. осн. мн., сильн.)</td>
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
                @if ($i>3)
            <td>{!! str_diff($lemma_bases[$i-3], $lemma_bases[$i]) !!}</td>                
                @endif
            @endfor
        </tr>
        @endforeach
    </table>
