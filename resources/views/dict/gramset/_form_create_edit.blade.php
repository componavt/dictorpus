        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
        
<div class="row">
    <div class="col-sm-6">
        @foreach ($grams as $code => $category_info)
            @include('widgets.form.formitem._select', 
                    ['name' => 'gram_id_'.$code, 
                     'values' => $category_info['grams'],
                     'title' => $category_info['name']
                    ]) 
        @endforeach
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._text',
                ['name' => 'sequence_number',
                 'attributes'=>['size' => 2],
                 'title' => trans('messages.sequence_number')])         

        @include('widgets.form.formitem._select', 
                ['name' => 'gramset_category_id', 
                 'values' => $gramset_category_values,
                 'title' => trans('dict.gramset_category')
                ]) 
                    
        @include('widgets.form.formitem._select2',
                ['name' => 'parts_of_speech',
                 'title' => trans('dict.pos'),
                 'values' => $pos_values, 
                 'value' => $pos_value, 
                 'grouped' => true
                ])

        @include('widgets.form.formitem._select2',
                ['name' => 'langs',
                 'title' => trans('dict.lang'),
                 'values' => $lang_values, 
                 'value' => $lang_value
                ])
@include('widgets.form.formitem._submit', ['title' => $submit_title])
    </div>
</div>    
