        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._select', 
                ['name' => 'motype_id', 
                 'values' =>$motype_values,
                 'title' => trans('corpus.motype')
        ])                  
        @include('widgets.form.formitem._select2', 
                ['name' => 'parent_id', 
                 'values' =>$parent_values,
                 'title' => trans('corpus.parent'),
                 'is_multiple'=>false,
                 'class'=>'select-motive form-control'
        ])                                  
        @include('widgets.form.formitem._text', 
                ['name' => 'code', 
                 'title'=>trans('messages.code')
        ])                 
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_russian')
        ])                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_english')
        ])  
        <br>
        @include('widgets.form.formitem._submit', ['title' => $submit_title])
    </div>
</div>                 
