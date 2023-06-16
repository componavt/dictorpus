{!! Form::open(['url' => $url, 'method' => 'get']) !!}
<div class="search-form row">
    <div class="col-md-3">
        @include('widgets.form.formitem._text',
                       ['name' => 'search_lemma',
                        'value'=> $url_args['search_lemma'],
                        'title' => trans('dict.lemma')])
    </div>
    <div class="col-sm-4">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_pos', 
                 'values' => $pos_values,
                 'value' => $url_args['search_pos'],
                 'title' => trans('dict.pos'),
        ])                 
    </div>
    <div class="col-sm-3">
        @include('widgets.form.formitem._select', 
                ['name' => 'search_status', 
                'values' => trans('dict.output_checked_or_not'),
                'value' => $url_args['search_status'],
                 'title' => trans('messages.output')] )
    </div>
    <div class="col-sm-2">
        <div class="search-button-b">       
            <span>{{trans('search.show_by')}}</span>
            <input placeholder="{{ trans('messages.limit_num') }}" class="form-control" id="limit_num" name="limit_num" type="text" value="{{ $url_args['limit_num'] }}" style="text-align: center;height: 25px;padding: 0;">
            <span>{{ trans('messages.records') }}</span>
        </div>
        <input class="btn btn-primary btn-default form-control" type="submit" value="{{ trans('messages.view') }}">
    </div>
</div>
{!! Form::close() !!}
