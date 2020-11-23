<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('dict.name').' '.trans('messages.in_russian')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_short_ru', 
                 'title'=>trans('dict.name_short').' '.trans('messages.in_russian')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('dict.name').' '.trans('messages.in_english')])                 
    </div>             
    <div class="col-sm-6">
        @include('widgets.form.formitem._select', 
                ['name' => 'category_id', 
                 'values' => $categories,
                 'title' => trans('messages.category')]) 
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'code', 
                 'title'=>trans('dict.code')])
        <br>         
        @include('widgets.form.formitem._checkbox_for_field', 
                ['name' => 'without_gram', 
                 'tail'=>trans('dict.without_gram')])         
    </div>             
</div>             

@include('widgets.form.formitem._submit', ['title' => $submit_title])
