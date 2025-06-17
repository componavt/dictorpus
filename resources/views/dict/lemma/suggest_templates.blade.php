@if (empty($templates))
<p>Пока для этого слова нет правила.</p>
@else
    @foreach ($templates as $i=>$template)
    <p><a href="#" onClick="insertTemplate('{{ $template }}')">{{ $template }}</a>@if (!empty($wordforms[$i])):
            {{ join(', ', $wordforms[$i]) }}
        @endif
    </p>
    @endforeach
@endif