<h3>{{$lemma->lemma}}</h3>
@foreach ($lemma->meaningsWithLabel($label_id) as $meaning) 
    {{$meaning->getMultilangMeaningTextsString('ru')}}<br>
@endforeach

@include('dict.meaning.form._create',
         ['count' => $i,
          'title' => '',
          'langs_for_meaning' => $langs_for_meaning])

<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'example', 
                 'special_symbol' => true,
                 'value' => '',
                 'title'=>'пример на карельском языке'])
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'example_ru', 
                 'value' => '',
                 'title'=>'перевод на русский язык'])
    </div>
</div>
          