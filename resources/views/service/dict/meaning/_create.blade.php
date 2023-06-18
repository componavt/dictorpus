<h3>{{$lemma->lemma}}</h3>
<input type='hidden' id='lemma_id' value='{{ $lemma->id }}'>
@foreach ($lemma->meaningsWithLabel($label_id) as $meaning) 
    {{$meaning->getMultilangMeaningTextsString('ru')}}<br>
@endforeach

<div style='text-decoration: line-through;'>
@foreach ($lemma->meaningsWithoutLabel($label_id) as $meaning) 
    {{$meaning->getMultilangMeaningTextsString('ru')}}<br>
@endforeach    
</div>
@include('service.dict.meaning._form_create',
        ['count' => '',
         'meaning_n' => 'Новое',
        ])
          