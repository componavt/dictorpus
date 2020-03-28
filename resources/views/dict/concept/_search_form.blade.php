        {!! Form::open(['url' => '/dict/concept/', 
                             'method' => 'get']) 
        !!}
<div class="search-b">
    <div>
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                 'value' => $url_args['search_id'],
                 'attributes'=>['placeholder' => 'ID', 'size'=>3]])                                  
    </div>
    <div>
        @include('widgets.form.formitem._select', 
                ['name' => 'search_category', 
                 'values' => $category_values,
                 'value' => $url_args['search_category'],
                 'attributes' => ['placeholder' => trans('messages.category')]]) 
    </div>
    <div>
         @include('widgets.form.formitem._text', 
                ['name' => 'search_text', 
                 'special_symbol' => true,
                 'value' => $url_args['search_text'],
                 'attributes'=>['placeholder' => trans('dict.concept')]])
    </div>
    <div class="search-button-b">   
        @include('widgets.form.formitem._search_button_with_show_by')
    </div>
</div>                 
        {!! Form::close() !!}
