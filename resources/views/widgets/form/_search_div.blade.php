<?php 
if (!isset($submit_value) || !$submit_value) {
    $submit_value = 'view';
}
if (!isset($cols) || !$cols) {
    $cols = 3;
}
?>
    <div class="col-sm-{{$cols}} search-button-b">       
        <span>
        {{trans('search.show_by')}}
        </span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['size' => 2,
                               'placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.'.$submit_value)])
    </div>
