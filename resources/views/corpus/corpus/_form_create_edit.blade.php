<div class="row">
    <div class="col-sm-8">
        @include('widgets.form.formitem._text',
                ['name' => 'name_en', 
                 'title'=>trans('corpus.name').' '.trans('messages.in_english')])

        @include('widgets.form.formitem._text',
                ['name' => 'name_ru',
                 'title'=>trans('corpus.name').' '.trans('messages.in_russian')])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._text',
                ['name' => 'short_en',
                 'title' => trans('corpus.short_en')])

        @include('widgets.form.formitem._text',
                ['name' => 'short_ru',
                 'title' => trans('corpus.short_ru')])
    </div>
</div>
@include('widgets.form.formitem._submit', ['title' => $submit_title])
