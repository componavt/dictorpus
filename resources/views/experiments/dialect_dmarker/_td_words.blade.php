            <td style="vertical-align: top">
                    @foreach ($variant->searchWords($dialect_id)->groupBy('word')->selectRaw('word, count(*) as count, text_id, w_id')->get() as $word)
                    <a href="{{ LaravelLocalization::localizeURL('/corpus/text/'.$word->text_id.'?search_wid='.$word->w_id)}}"
                       title="{{$dialect_info['name']}}">{{$word->word}}</a>
                        @if ($word->count >1)
                    ({{$word->count}})
                        @endif
                        <br>
                    @endforeach
            </td>
