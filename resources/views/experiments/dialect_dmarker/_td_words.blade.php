            <td colspan='2' style="vertical-align: top">
                    @foreach ($variant['words'] as $word)
                    <a href='/ru/corpus/sentence/results?search_dialect[]={{$dialect_id}}&search_words[1][w]="^{{$word->word}}$"'
                       title="{{$dialect_info['name']}}">{{$word->word}}</a>
                        @if ($word->count >1)
                    ({{$word->count}})
                        @endif
                        <br>
                    @endforeach
            </td>
