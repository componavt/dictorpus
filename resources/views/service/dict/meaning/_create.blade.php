<h3>{{$lemma->lemma}}</h3>
<input type='hidden' id='lemma_id' value='{{ $lemma->id }}'>
@foreach ($lemma->meaningsWithLabel($label_id) as $meaning) 
    {{$meaning->getMultilangMeaningTextsString('ru')}}<br>
@endforeach

@include('service.dict.meaning._form_create',
        ['count' => '',
         'meaning_n' => 'Новое',
        ])
          