<input type="hidden" id='meaning-id' value=''>

@include('widgets.form.formitem._select',
        ['name' => 'label_id',
         'values' =>$label_values,
         'title' => trans('dict.choose_label')])

<h3>или создайте новую</h3>

<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'label_name_ru', 
                 'title'=>trans('dict.name').' '.trans('messages.in_russian')])
        @include('widgets.form.formitem._text', 
                ['name' => 'label_short_ru', 
                 'title'=>trans('messages.short').' '.trans('messages.in_russian')])
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'label_name_en', 
                 'title'=>trans('dict.name').' '.trans('messages.in_english')])                                         
        @include('widgets.form.formitem._text', 
                ['name' => 'label_short_en', 
                 'title'=>trans('messages.short').' '.trans('messages.in_english')])                                         
    </div>
</div>