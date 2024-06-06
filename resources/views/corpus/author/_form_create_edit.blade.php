@include('widgets.form._url_args_by_post',['url_args'=>$url_args])
<input type="hidden" id="author_field" value="">
<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('messages.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('messages.name').' '.trans('messages.in_russian')])
    </div>                 
    <div class="col-sm-6">
    @foreach ($project_langs as $lang) 
        @include('widgets.form.formitem._text', 
                ['name' => 'names['.$lang->id.']', 
                 'value'=> $author ? $author->getNameByLang($lang->id) : null,
                 'title'=>trans('messages.name').' ('.$lang->name.')'])
    @endforeach                 
    </div>
</div>
