            <td style="text-align: right">
                <a href='/ru/corpus/sentence/results?search_dialect[]={{$dialect_id}}&search_words[1][w]="{{$variant->template}}"'
                   style="text-decoration: none; color: {{$variant->rightFrequency($dialect_id) ? 'black' : 'red'}}">
                {{ $output == 'frequency' ? $variant->frequency($dialect_id) : $variant->fraction($dialect_id) }}
                </a>
            </td>
