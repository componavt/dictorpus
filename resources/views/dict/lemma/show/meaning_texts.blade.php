            @if (isset($meaning_texts[$meaning->id]))
            <ul>
                @foreach ($meaning_texts[$meaning->id] as $lang_name => $meaning_text)
                <li><b>{{$lang_name}}:</b> {{$meaning_text}}</li>
                @endforeach
            </ul>
            @endif
