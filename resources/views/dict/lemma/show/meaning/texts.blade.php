            @if (isset($meaning_texts[$meaning->id]))
            <ul>
                @foreach ($meaning_texts[$meaning->id] as $lang_name => $meaning_text)
                <li><b>{{$lang_name}}:</b> {!!highlight($meaning_text, $url_args['search_w'], 'search-word')!!}</li>
                @endforeach
            </ul>
            @endif
