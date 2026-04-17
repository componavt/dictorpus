        {!! Form::open(['url' => '/dict/label/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._text',
                ['name' => 'search_name',
                 'value' =>$url_args['search_name'],
                 'attributes'=> ['placeholder'=>trans('corpus.name')] ])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._radio', 
                ['name' => 'search_visible', 
                 'values' => trans('dict.visible_values'),
                 'checked' => $url_args['search_visible']!==null ? (int)$url_args['search_visible']  : null])
    </div>
    @include('widgets.form._search_div')
</div>                 
        {!! Form::close() !!}
