        {!! Form::open(['url' => '/corpus/bible/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-md-2">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_id', 
                'value' => $url_args['search_id'] ? $url_args['search_id'] : '',
                'title' => 'ID'])
    </div>
    <div class="col-md-8">
         @include('widgets.form.formitem._text', 
                ['name' => 'search_name', 
                'value' => $url_args['search_name'],
                'title'=> trans('corpus.name')])
    </div>
    <div class="col-md-2" style='text-align:right; padding-top:27px'>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>
        {!! Form::close() !!}
