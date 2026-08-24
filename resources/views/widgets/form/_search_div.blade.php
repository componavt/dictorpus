    <div class="col-sm-4 search-button-b" @if (!empty($rows) && $rows==2) 
        style="padding-top: 26px"
        @endif>     
        <span>
        {{trans('search.show_by')}}
        </span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>['placeholder' => trans('messages.limit_num') ]]) 
        <span>
                {{ trans('messages.records') }}
        </span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.'.($submit_value ?? 'view'))])
    </div>
