        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_russian')])
                 
        @include('widgets.form.formitem._select', 
                ['name' => 'region_id', 
                 'values' =>$region_values,
                 'title' => trans('corpus.region')]) 

