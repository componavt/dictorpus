<div style='padding-top: 20px'>
    <b>{{ $meaning_n}} значение</b>
    @include('widgets.form.formitem._text',
            ['name' => 'meaning'.$count,
             'special_symbol' => true,
             'value' => ''
            ])
</div>

<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'example'.$count, 
                 'special_symbol' => true,
                 'value' => '',
                 'title'=>'пример на карельском языке'])
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'example_ru'.$count, 
                 'value' => '',
                 'title'=>'перевод на русский язык'])
    </div>
</div>
          