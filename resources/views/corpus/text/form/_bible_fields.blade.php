<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._select2',
                ['name' => 'bibles[bible_id]', 
                 'values' =>$bible_values,
                 'value' => $text && $text->bibles()->first() ? $text->bibles()->first()->id : null,
                 'is_multiple' => false,
                 'title' => trans('corpus.bible'),
                 'class'=>'multiple-select-bible form-control'
            ])
    </div>
    <div class="col-sm-2">
        @include('widgets.form.formitem._text',
                ['name' => 'bibles[chapter]', 
                 'value' => $text && $text->bibles()->first() ? $text->bibles()->first()->pivot->chapter : null,
                 'title' => trans('corpus.chapter'),
            ])
    </div>
    <div class="col-sm-4">
        <p style="margin-bottom: 5px"><b>{{ trans('corpus.verses') }}</b></p>
        <div style="display: flex">
            <span style='margin-right: 10px'>{{ trans('messages.from') }}</span>
            @include('widgets.form.formitem._text',
                    ['name' => 'bibles[verse_from]',
                     'value' => $text && $text->bibles()->first() ? $text->bibles()->first()->pivot->verse_from : null
                    ])
            <span style='margin: 0 10px'>{{ trans('corpus.to') }}</span>
            @include('widgets.form.formitem._text',
                    ['name' => 'bibles[verse_to]',
                     'value' => $text && $text->bibles()->first() ? $text->bibles()->first()->pivot->verse_to : null
                    ])
        </div>
    </div>
</div>
