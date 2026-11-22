{!! Form::open(['url' => '/dict/dialect/', 'method' => 'get']) !!}
<div class="search-form row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'search_lang',
                 'values' => $lang_values,
                 'value' => $url_args['search_lang'],
                 'attributes' => ['placeholder' => trans('dict.lang')]])
    </div>
    <div class="col-sm-4">
                @include('widgets.form.formitem._text',
                ['name' => 'search_name',
                 'value' => $url_args['search_name'],
                 'help_func' => "callHelp('help-text-fields')",
                 'attributes' => ['placeholder' => trans('dict.name')]])
    </div>
    @include('widgets.form._search_div')
</div>
{!! Form::close() !!}