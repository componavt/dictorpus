@if ($text->title)
        <h3>
        @if ($text->authors)
            {{$text->authorsToString()}}<br>
        @endif
        {{ $text->title }}
        @if (User::checkAccess('corpus.edit'))
            @include('widgets.form.button._edit', 
                     ['route' => '/corpus/text/'.$text->id.'/sentences',
                      'title' => 'редактировать предложения',
                      'without_text' => 1])
        @endif            
        </h3>
        <h5>
        ({{ $text->lang->name }}@if ($text->dialectsToString()), {{$text->dialectsToString()}}@endif)
        </h5>
@endif      

@if ($text->text)
            <div id="text">{!! $text->textForPage($url_args); !!}</div>
@endif      
