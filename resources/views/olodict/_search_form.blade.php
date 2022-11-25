<form id="search-form">
    @include('widgets.form.formitem._text',
            ['name' => 'search_word',
             'special_symbol' => true,
             'full_special_list' => false,
             'value' => isset($url_args['search_word']) ? $url_args['search_word'] : '',
             'attributes' => ['onKeyUp' => "searchLemmas('$locale')",
                             'placeholder' => trans('olodict.word') ]]) 
    <!--div class="form-group ">
        <input id="search_word" type='text' onKeyUp="searchLemmas('{{$locale}}')" placeholder="{{trans('olodict.word')}}"
               value="{{isset($url_args['search_word']) ? $url_args['search_word'] : ''}}">
    </div-->
    @include('widgets.form.formitem._select',
            ['name' => 'search_pos',
             'values' =>$pos_values,
             'value' => isset($url_args['search_pos']) ? $url_args['search_pos'] : null,
             'attributes' => ['onChange' => "searchLemmas('$locale')",
                              'placeholder' => trans('dict.pos')]]) 

    @include('widgets.form.formitem._text',
            ['name' => 'search_meaning',
            'value' => isset($url_args['search_meaning']) ? $url_args['search_meaning'] : '',
            'attributes' => ['onKeyUp' => "searchLemmas('$locale')",
                             'placeholder' => trans('dict.interpretation') ]]) 

    @include('widgets.form.formitem._select',
            ['name' => 'search_concept_category',
             'values' => $concept_category_values,
             'value' =>$url_args['search_concept_category'],
             'attributes' => ['onChange' => "searchLemmas('$locale')",
                              'placeholder' => trans('olodict.topic')]]) 

    @include('widgets.form.formitem._select2',
            ['name' => 'search_concept', 
             'is_multiple' => false,
             'values' => $concept_values,
             'value' => $url_args['search_concept'],
             'event' => "onChange = searchLemmas('$locale')",
             'class'=>'select-concept form-control']) 
                              
{{--    @include('widgets.form.formitem._checkbox',
            ['name' => 'with_audios',
            'value' => 1,
            'checked' => $url_args['with_audios']==1,
            'attributes' => ['onClick' => "searchLemmas('$locale')"],
            'tail'=>trans('dict.with_audios')]) --}}

    @include('widgets.form.formitem._checkbox',
            ['name' => 'with_photos',
            'value' => 1,
            'checked' => $url_args['with_photos']==1,
            'attributes' => ['onClick' => "searchLemmas('$locale')"],
            'tail'=>trans('dict.with_photos')]) 

    @include('widgets.form.formitem._checkbox',
            ['name' => 'with_template',
            'value' => 1,
            'checked' => $url_args['with_template']==1,
            'attributes' => ['onClick' => "searchLemmas('$locale')"],
            'tail'=>trans('olodict.with_template')]) 

    <div class="form-group ">
        <input type='button' value="{{trans('messages.reset')}}" class='btn btn-primary btn-default' onClick="resetSearchForm()">
    </div>
</form>                          