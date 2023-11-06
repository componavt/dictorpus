@include('widgets.form._url_args_by_post',['url_args'=>$url_args])
<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._select',
                ['name' => 'concept_category_id',
                 'values' =>$concept_category_values,
                 'title' => trans('messages.category')])

        @include('widgets.form.formitem._select',
                ['name' => 'pos_id',
                 'values' =>$pos_values,
                 'title' => trans('dict.pos')])                 
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'text_ru', 
                 'title'=>trans('dict.name').' '.trans('messages.in_russian')])
                                
        @include('widgets.form.formitem._text', 
                ['name' => 'text_en', 
                 'title'=>trans('dict.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form.formitem._submit', ['title' => $submit_title])
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'wiki_photo', 
                 'title'=>trans('dict.wiki_photo')])
                                
        @include('widgets.form.formitem._textarea', 
                ['name' => 'descr_ru',
                 'attributes'=>['rows'=>3],
                 'title'=>trans('dict.descr').' '.trans('messages.in_russian')])
                                
        @include('widgets.form.formitem._textarea', 
                ['name' => 'descr_en', 
                 'attributes'=>['rows'=>3],
                 'title'=>trans('dict.descr').' '.trans('messages.in_english')])                 
    </div>
</div>