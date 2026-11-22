@include('widgets.form._url_args_by_post', ['url_args' => $url_args])

<div class="row">
    <div class="col-sm-8">
        @include('widgets.form.formitem._select',
                ['name' => 'lang_id',
                 'values' => $lang_values,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id' => 'lemma_lang_id']])

        @include('widgets.form.formitem._text',
                ['name' => 'name_ru',
                 'title' => trans('dict.name').' '.trans('messages.in_russian')])

        @include('widgets.form.formitem._text',
                ['name' => 'name_en',
                 'title' => trans('dict.name').' '.trans('messages.in_english')])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._text',
                ['name' => 'sequence_number',
                 'title' => trans('messages.sequence_number')])

        @include('widgets.form.formitem._text',
                ['name' => 'short_ru',
                 'title' => trans('dict.dialect_short_ru')])

        @include('widgets.form.formitem._text',
                ['name' => 'code',
                 'title' => trans('dict.code')])
    </div>
</div>

@include('widgets.form.formitem._submit', ['title' => $submit_title])