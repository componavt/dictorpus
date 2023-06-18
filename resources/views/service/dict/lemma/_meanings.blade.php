@foreach ($lemma->meaningsWithLabel($label_id) as $meaning) 
    <div id='meaning-{{$meaning->id}}'>
        <i class="fa fa-times fa-lg clickable" data-delete="{{csrf_token()}}" 
           onClick="removeLabelMeaning({{ $lemma->id }}, {{ $meaning->id }}, {{ $label_id }}, '{{$meaning->getMultilangMeaningTextsString('ru')}}')"></i>
        @if ($lemma->meanings()->count()>1)
        {{$meaning->meaning_n}})
        @endif
        <div id='b-meaning-{{$meaning->id}}' style='display: inline'> 
            @include('service.dict.meaning._view')
        </div>
    @foreach ($meaning->examples as $example) 
        @include('dict.example.view', 
            ['meaning_id'=>$meaning->id, 
             'example_obj'=>$example])
    @endforeach
        <i id="add-example-for-{{$meaning->id}}" class="fa fa-plus fa-lg clickable blue-color" onClick="addSimpleExample({{$meaning->id}})"></i>                        
    </div>
@endforeach

@foreach ($lemma->meaningsWithoutLabel($label_id) as $meaning) 
    <div id='meaning-{{$meaning->id}}' style='text-decoration: line-through;'>
        <i class="fa fa-plus fa-lg clickable link-color" onClick="addLabelMeaning({{ $lemma->id }}, {{ $meaning->id }}, {{ $label_id }})"></i>                        
        {{$meaning->getMultilangMeaningTextsString('ru')}}
    @foreach ($meaning->examples as $example) 
        @include('dict.example.view', 
            ['meaning_id'=>$meaning->id, 
             'example_obj'=>$example, 
             'access_edition'=>false])
    @endforeach
    </div>
@endforeach
