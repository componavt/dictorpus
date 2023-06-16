<h3>{{$lemma->lemma}}</h3>
<input type='hidden' id='lemma_id' value='{{ $lemma->id }}'>
@foreach ($lemma->meaningsWithLabel($label_id) as $meaning) 
    {{$meaning->getMultilangMeaningTextsString('ru')}}<br>
@endforeach

<div style='padding-top: 20px'>
    <b>Новое значение</b>
    @include('widgets.form.formitem._text',
            ['name' => 'new_meaning',
             'special_symbol' => true,
             'value' => ''
            ])
</div>

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
          