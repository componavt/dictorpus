<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select', 
                ['name' => 'corpus_id', 
                 'values' =>$corpus_values,
                 'title' => trans('corpus.corpus')]) 
                 
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'genres', 
                 'values' =>$genre_values,
                 'value' => $genre_value ?? null,
                 'title' => trans('navigation.genres'),
                 'class'=>'multiple-select-genre form-control'
            ])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'cycles', 
                 'values' =>$cycle_values,
                 'value' => $cycle_value ?? null,
                 'title' => trans('navigation.cycles'),
                 'class'=>'multiple-select-cycle form-control'
            ])
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'dialects', 
                 'values' =>$dialect_values,
                 'value' => $dialect_value ?? null,
                 'title' => trans('navigation.dialects'),
                 'class'=>'select-dialect form-control'
            ])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'plots', 
                 'values' =>$plot_values,
                 'value' => $plot_value ?? null,
                 'title' => trans('navigation.plots'),
                 'class'=>'multiple-select-plot form-control'
            ])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2',
                ['name' => 'topics', 
                 'values' =>$topic_values,
                 'value' => $topic_value ?? null,
                 'call_add_onClick' => "addTopic()",
                 'title' => trans('navigation.topics'),
                 'class'=>'select-topic form-control'
            ])
    </div>
</div>                 
