<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_english')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_russian')])
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
