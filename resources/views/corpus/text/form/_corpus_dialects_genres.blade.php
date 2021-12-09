<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select', 
                ['name' => 'corpus_id', 
                 'values' =>$corpus_values,
                 'title' => trans('corpus.corpus')]) 
                 
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'dialects', 
                 'values' =>$dialect_values,
                 'value' => $dialect_value,
                 'title' => trans('navigation.dialects'),
                 'class'=>'select-dialect form-control'
            ])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'genres', 
                 'values' =>$genre_values,
                 'value' => $genre_value,
                 'title' => trans('navigation.genres'),
                 'class'=>'multiple-select-genre form-control'
            ])
    </div>
</div>                 
