        {!! Form::open(['url' => '/dict/dialect/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-sm-8">
        @include('widgets.form.formitem._select',
                ['name' => 'search_lang',
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=> ['placeholder'=>trans('dict.lang')] ])
    </div>
    @include('widgets.form._search_div')
</div>                 
        {!! Form::close() !!}
