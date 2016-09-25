        @include('widgets.form._formitem_select', 
                ['name' => 'gram_category_id', 
                 'values' => $gram_categories,
                 'title' => trans('dict.gram_category')]) 
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'name_short_en', 
                 'title'=>trans('dict.name_short').' '.trans('messages.in_english')])
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'name_en', 
                 'title'=>trans('dict.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'name_short_ru', 
                 'title'=>trans('dict.name_short').' '.trans('messages.in_russian')])
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'name_ru', 
                 'title'=>trans('dict.name').' '.trans('messages.in_russian')])
                 
        @include('widgets.form._formitem_text',
                ['name' => 'sequence_number',
                 'attributes'=>['size' => 2],
                 'title' => trans('messages.sequence_number')])         

@include('widgets.form._formitem_btn_submit', ['title' => $submit_title])
