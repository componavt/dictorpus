        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('corpus.informant_name').' '.trans('messages.in_english')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('corpus.informant_name').' '.trans('messages.in_russian')])
                 
        @include('widgets.form.formitem._select', 
                ['name' => 'birth_place_id', 
                 'values' =>$place_values,
                 'title' => trans('corpus.birth_place')]) 
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'birth_date', 
                 'title'=>trans('corpus.year_of_birth')])

@include('widgets.form.formitem._submit', ['title' => $submit_title])
