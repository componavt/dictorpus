<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'corpuses', 
                 'values' =>$corpus_values,
                 'value' => $text? $text->corpusValue() : [1],
                 'class'=>'multiple-select select-corpus form-control',
                 'title' => trans('corpus.corpus')]) 
                 
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'dialects', 
                 'values' =>$dialect_values,
                 'value' => $text? $text->dialectValue() : [],
                 'title' => trans('navigation.dialects'),
                 'class'=>'select-dialect form-control'
            ])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'genres', 
                 'values' =>$genre_values,
                 'value' => $text? $text->genreValue() : [],
                 'title' => trans('navigation.genres'),
                 'class'=>'multiple-select-genre form-control'
            ])
    </div>
</div>
