@include('widgets.form.formitem._textarea', 
        ['name' => 'fragment', 
         'title' => 'фрагмент предложения',
         'value' => $fragment ?? '',
         'special_symbol' => true,
         'attributes' => ['rows'=>2]])

@include('widgets.form.formitem._textarea', 
        ['name' => 'translations[2]', 
         'title' => 'перевод на русский язык',
         'value' => $translations[2] ?? '',
         'attributes' => ['rows'=>2]])
