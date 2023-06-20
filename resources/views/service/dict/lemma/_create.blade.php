@include('service.dict.lemma._edit', 
        ['lemma'=>null,
         'phrase_values' => []])
        
@for ($i=0; $i<$total_meanings; $i++)
    @include('service.dict.meaning._form_create',
            ['count' => $i,
             'meaning_n' => $i+1,
             'title'=>'' ])
@endfor
        
        



