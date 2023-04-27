            <td style="text-align: {{$output=='frequency' ? 'right' : 'left'}}">
                <a href='/ru/corpus/sentence/results?search_dialect[]={{$dialect_id}}&search_words[1][w]="{{$template}}"'
                   style="text-decoration: none; color: {{ $variant['color'] }}" title="{{$dialect_info['name']}}">
                {{ $variant['t_'.$output] }}
                </a>
            </td>
            <td style="text-align: {{$output=='frequency' ? 'right' : 'left'}}">
                {{ $variant['w_'.$output] }}
            </td>
