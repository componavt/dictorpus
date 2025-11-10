        @include('widgets.form._url_args_by_post',['url_args'=>$url_args])
<div class="row">
    <div class="col-sm-8">
        @include('widgets.form.formitem._text', 
                ['name' => 'author', 
                 'title'=>trans('monument.author')])   
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'title', 
                 'title'=>trans('corpus.name')])                 
                 
        <div class="form-group">
            <label>{{ trans('monument.publ_date') }}</label>
            <div class='flex-hor-group' style="justify-content: flex-start;">
                <span style='margin-right: 0.5rem'>с</span>
                @include('widgets.form.formitem._text', 
                        ['name' => 'publ_date_from',
                         'value' => old('publ_date_from') ? old('publ_date_from') : ($monument ? $monument->publ_date_from_for_form : null),
                         'attributes'=> ['placeholder' => 'мм.гггг']])  
                         
                <span style='margin: 0 0.5rem 0 2rem'>по</span>                         
                @include('widgets.form.formitem._text', 
                        ['name' => 'publ_date_to',
                         'value' => old('publ_date_to') ? old('publ_date_to') : ($monument ? $monument->publ_date_to_for_form : null),
                         'attributes'=> ['placeholder' => 'мм.гггг']])     
            </div>
        </div>

        @include('widgets.form.formitem._textarea', 
                ['name' => 'bibl_descr', 
                 'attributes' => ['rows' => 3],
                 'title'=>trans('monument.bibl_descr')])     
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'dcopy_link', 
                 'title'=>trans('monument.dcopy_link')])     
                 
        @include('widgets.form.formitem._textarea', 
                ['name' => 'publ', 
                 'attributes' => ['rows' => 3],
                 'title'=>trans('monument.publ')])     
                 
        @include('widgets.form.formitem._textarea', 
                ['name' => 'study', 
                 'attributes' => ['rows' => 3],
                 'title'=>trans('monument.study')])     
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'archive', 
                 'title'=>trans('monument.archive')])     
                 
        @include('widgets.form.formitem._textarea', 
                ['name' => 'comment', 
                 'attributes' => ['rows' => 3],
                 'title'=>trans('monument.comment')])     
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select2', 
                ['name' => 'langs', 
                 'values' =>$lang_values,
                 'value' => $monument ? $monument->langValue() : [],
                 'title' => trans('dict.lang'),
                 'class' => 'select-lang form-control'])
                 
        @include('widgets.form.formitem._select2', 
                ['name' => 'dialects', 
                 'values' =>$dialect_values,
                 'value' => $monument ? $monument->dialectValue() : [],
                 'title' => trans('dict.dialect'),
                 'class'=>'select-dialect form-control'
        ])
        
        @include('widgets.form.formitem._text', 
                ['name' => 'place', 
                 'title'=>trans('monument.place')])     
        
        @include('widgets.form.formitem._select', 
                ['name' => 'graphic_id', 
                 'values' =>[NULL=>'']+trans('monument.graphic_values'),
                 'title' => trans('monument.graphic')])
                 
        @include('widgets.form.formitem._radio', 
                ['name' => 'has_trans', 
                 'values' =>trans('monument.has_trans_values'),
                 'title' => trans('monument.has_trans')])
                 
        @include('widgets.form.formitem._select2', 
                ['name' => 'types', 
                 'values' =>[NULL=>'']+trans('monument.type_values'),
                 'value' => $monument ? $monument->types : [],
                 'class' => 'select-type form-control',
                 'title' => trans('monument.type')])
                 
        @include('widgets.form.formitem._radio', 
                ['name' => 'is_printed', 
                 'values' =>trans('monument.is_printed_values'),
                 'title' => trans('monument.is_printed')])
                 
        @include('widgets.form.formitem._radio', 
                ['name' => 'is_full', 
                 'values' =>trans('monument.is_full_values'),
                 'title' => trans('monument.is_full')])
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'volume', 
                 'title'=>trans('monument.volume')])     
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'pages', 
                 'title'=>trans('monument.pages')])     
                 
    </div>
</div>                 
@include('widgets.form.formitem._submit', ['title' => $submit_title])
