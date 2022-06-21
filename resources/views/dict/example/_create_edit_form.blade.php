@include('widgets.form.formitem._text', 
        ['name' => 'example-'.$example_id, 
         'special_symbol' => true,
         'value' => $example ?? '',
         'attributes'=>['placeholder'=> 'пример на карельском языке']])

@include('widgets.form.formitem._text', 
        ['name' => 'example_ru-'.$example_id, 
         'value' => $example_ru ?? '',
         'attributes'=>['placeholder'=> 'перевод на русский язык']])

<input type="button" class="btn btn-primary btn-default" value="{{trans('messages.save')}}" onclick="{{$func}}">         
