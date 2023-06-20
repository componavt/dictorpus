@foreach ($lemma->meaningsWithLabel($label_id) as $meaning) 
    <div id='meaning-{{$meaning->id}}'>
        @if ($meaning->meaning_n > 1) 
            <a onClick="meaningUp({{ $lemma->id }}, {{ $meaning->id}}, {{ $label_id }})" 
               class='up' title="Переместить вверх по списку">&uarr;</a>
        @else
            &nbsp;&nbsp;
        @endif
        @if ($meaning->meaning_n < $lemma->maxMeaningN())
            <a onClick="meaningDown({{ $lemma->id }}, {{ $meaning->id}}, {{ $label_id }})" 
               class='down' title="Переместить вниз по списку">&darr;</a>
        @else
            &nbsp;&nbsp;
        @endif
        
        <i class="fa fa-times fa-lg clickable" title="удалить значение из словаря"
           onClick="removeLabelMeaning({{ $lemma->id }}, {{ $meaning->id }}, {{ $label_id }}, '{{$meaning->getMultilangMeaningTextsString('ru')}}')"></i>
        @if ($lemma->meanings()->count()>1)
        {{$meaning->meaning_n}})
        @endif
        
        <div id='b-labels-{{$meaning->id}}' style='display: inline; vertical-align: super; font-size: 8px'>
            @include('service.dict.label._index')
        </div>
        
        <div id='b-meaning-{{$meaning->id}}' style='display: inline'> 
            @include('service.dict.meaning._view')
        </div>
    @foreach ($meaning->examples as $example) 
        @include('dict.example.view', 
            ['meaning_id'=>$meaning->id, 
             'example_obj'=>$example])
    @endforeach
        <i id="add-example-for-{{$meaning->id}}" title="Добавить новый пример"
           class="fa fa-plus fa-lg clickable blue-color" 
           onClick="addSimpleExample({{$meaning->id}})"></i>                        
    </div>
@endforeach

@foreach ($lemma->meaningsWithoutLabel($label_id) as $meaning) 
    <div id='meaning-{{$meaning->id}}' style='text-decoration: line-through;'>
        <i class="fa fa-plus fa-lg clickable link-color" 
           title="Включить значение в словарь"
           onClick="addLabelMeaning({{ $lemma->id }}, {{ $meaning->id }}, {{ $label_id }})"></i>                        
        {{$meaning->getMultilangMeaningTextsString('ru')}}
    @foreach ($meaning->examples as $example) 
        @include('dict.example.view', 
            ['meaning_id'=>$meaning->id, 
             'example_obj'=>$example, 
             'access_edition'=>false])
    @endforeach
    </div>
@endforeach
