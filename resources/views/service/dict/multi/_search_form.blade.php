{!! Form::open(['url' => $url, 'method' => 'get']) !!}
<div class="search-form row">
    <div class="col-md-2">
        @include('widgets.form.formitem._text', 
                ['name' => 'search_lemma', 
                 'value' => $url_args['search_lemma'],
                 'title' => trans('dict.lemma'),
        ])                 
    </div>
    <div class="col-md-3">
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
    <div class="col-sm-4 search-button-b" style='padding-top: 25px'>       
        <span>{{trans('search.show_by')}}</span>
        @include('widgets.form.formitem._text', 
                ['name' => 'limit_num', 
                'value' => $url_args['limit_num'], 
                'attributes'=>[ 'placeholder' => trans('messages.limit_num') ]]) 
        <span>{{ trans('messages.records') }}</span>
        @include('widgets.form.formitem._submit', ['title' => trans('messages.view')])
    </div>
</div>
{!! Form::close() !!}
