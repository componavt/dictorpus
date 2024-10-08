<form id="search-form">
    <h2>{{trans('olodict.search_by_form')}}</h2>    
    @include('widgets.form.formitem._text',
            ['name' => 'search_word',
             'special_symbol' => true,
             'special_sym' => '|',
             'full_special_list' => false,
             'value' => isset($url_args['search_word']) ? $url_args['search_word'] : '',
             'attributes' => ['onKeyUp' => "clearLetters(); searchLemmas('$locale')",
                             'placeholder' => trans('olodict.word') ]]) 
    <!--div class="form-group ">
        <input id="search_word" type='text' onKeyUp="searchLemmas('{{$locale}}')" placeholder="{{trans('olodict.word')}}"
               value="{{isset($url_args['search_word']) ? $url_args['search_word'] : ''}}">
    </div-->
    @include('widgets.form.formitem._text',
            ['name' => 'search_meaning',
            'value' => isset($url_args['search_meaning']) ? $url_args['search_meaning'] : '',
            'attributes' => ['onKeyUp' => "searchLemmas('$locale')",
                             'placeholder' => trans('dict.interpretation') ]]) 

    @include('widgets.form.formitem._select2',
            ['name' => 'search_concept_category',
             'values' => $concept_category_values,
             'value' =>$url_args['search_concept_category'],
             'event' => "onChange = searchLemmas('$locale')",
             'class'=>'select-topic form-control']) 

    @include('widgets.form.formitem._select2',
            ['name' => 'search_concept', 
             'is_multiple' => false,
             'values' => $concept_values,
             'value' => $url_args['search_concept'],
             'event' => "onChange = searchLemmas('$locale')",
             'class'=>'select-concept form-control']) 
                              
    @include('widgets.form.formitem._select2',
            ['name' => 'search_pos',
             'values' =>$pos_values,
             'value' => isset($url_args['search_pos']) ? $url_args['search_pos'] : null,
             'event' => "onChange = searchLemmas('$locale')",
             'class'=>'select-pos form-control']) 

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
            'tail'=>trans('olodict.exact_search')]) 

    <div class="form-group ">
        <input type='button' value="{{trans('messages.reset')}}" class='btn btn-primary btn-default' onClick="resetSearchForm('{{ $locale }}')">
    </div>
</form>                          