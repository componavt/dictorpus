        <a href="{{ LaravelLocalization::localizeURL("/dict/lemma/".$phrase->id) }}">{{ $phrase->lemma }}</a>
    @if ($phrase->meanings && isset($phrase->meanings[0]))
        {{ $phrase->meanings[0]->getMeaningTextByLangCode('ru') }}
    @endif
        <i class="fa fa-pencil-alt fa-lg clickable link-color" 
           onClick="editPhrase({{$phrase->id}})" title="Изменить фразу"></i>
