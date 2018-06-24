        {!! Form::open(['url' => '/dict/dialect/', 
                             'method' => 'get', 
                             'class' => 'form-inline']) 
        !!}
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form._formitem_select', 
                ['name' => 'lang_id', 
                 'values' =>$lang_values,
                 'value' =>$lang_id,
                 'attributes'=>['placeholder' => trans('dict.select_lang') ]]) 
    </div>
    <div class="col-sm-4 search-button-b">       
        <span>
        {{trans('messages.show_by')}}
        </span>
        @include('widgets.form._formitem_text', 
                ['name' => 'limit_num', 
                'value' => $limit_num, 
                'attributes'=>['size' => 5,
                               'placeholder' => trans('messages.limit_num') ]])
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.view')])
    </div>
</div>                 
        {!! Form::close() !!}
