        {!! Form::open(['url' => '/corpus/monument/', 
                             'method' => 'get']) 
        !!}
<div class="search-form row">
    <div class="col-sm-3">
         @include('widgets.form.formitem._text', 
                ['name' => 'search_title', 
                'value' => $url_args['search_title'],
                'attributes'=>['placeholder' => trans('corpus.name')]])
    </div>
    <div class="col-sm-3">
        @include('widgets.form.formitem._select',
                ['name' => 'search_lang',
                 'values' =>$lang_values,
                 'value' =>$url_args['search_lang'],
                 'attributes'=>['placeholder' => trans('dict.lang')]])
    </div>
    <div class="col-sm-3">
        @include('widgets.form.formitem._select2',
                ['name' => 'search_dialects',
                 'values' =>$dialect_values,
                 'value' =>$url_args['search_dialects'],
                 'class'=>'select-dialects form-control']) 
    </div>
    <div class="col-sm-3 search-button-b">       
        <span>
        {{ trans('search.show_by') }}
        </span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}
