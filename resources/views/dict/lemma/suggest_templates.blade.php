@if (empty($templates))
<p>Пока для этого слова нет правила.</p>
@else
    @foreach ($templates as $template)
    <p><a href="#" onClick="insertTemplate('{{ $template }}')">{{ $template }}</a></p>
    @endforeach
@endif