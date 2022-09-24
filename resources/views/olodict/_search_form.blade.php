<form id="search-form">
    <div class="form-group ">
        <input id="search_template" type='text' onKeyUp="searchLemmas('{{$locale}}')" placeholder="{{trans('olodict.word')}}"
               value="{{isset($url_args['search_template']) ? $url_args['search_template'] : ''}}">
    </div>
    @include('widgets.form.formitem._select',
            ['name' => 'search_pos',
             'values' =>$pos_values,
             'value' => isset($url_args['search_pos']) ? $url_args['search_pos'] : null,
             'attributes' => ['onChange' => "searchLemmas('$locale')",
                              'placeholder' => trans('dict.pos')]]) 

    <div class="form-group ">
        <input id="search_meaning" type='text' onKeyUp="searchLemmas('{{$locale}}')" placeholder="{{trans('dict.interpretation')}}" 
               value="{{isset($url_args['search_meaning']) ? $url_args['search_meaning'] : ''}}">
    </div>

    @include('widgets.form.formitem._checkbox',
            ['name' => 'with_audios',
            'value' => 1,
            'checked' => $url_args['with_audios']==1,
            'attributes' => ['onClick' => "searchLemmas('$locale')"],
            'tail'=>trans('dict.with_audios')]) 

    <div class="form-group ">
        <input type='button' value="{{trans('messages.reset')}}" class='btn btn-primary btn-default' onClick="resetSearchForm()">
    </div>
</form>                          