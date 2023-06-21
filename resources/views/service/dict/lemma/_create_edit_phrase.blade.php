@include('widgets.form.formitem._text', 
        ['name' => 'phrase-'.$phrase_id, 
         'value' => $phrase ?? '',
         'attributes'=>['placeholder'=> 'Фраза на карельском языке']])

@include('widgets.form.formitem._text', 
        ['name' => 'phrase_ru-'.$phrase_id, 
         'value' => $phrase_ru ?? '',
         'attributes'=>['placeholder'=> 'перевод на русский язык']])

<input type="button" class="btn btn-primary btn-default" value="{{trans('messages.save')}}" onclick="{{$func}}">         
