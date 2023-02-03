{!! Form::open(['url' => route('audio.list.create'),
                'method' => 'get'])
!!}
<div class="row">
    <div class="col-sm-4">
        @include('widgets.form.formitem._select',
                ['name' => 'search_dialect',
                 'values' =>$dialect_values,
                 'value' =>$url_args['search_dialect'],
                 'title' => trans('dict.dialect_usage')]) 
    </div>
    <div class="col-sm-4"><br>
        @include('widgets.form.formitem._submit', ['title' => trans('dict.create_list')])
    </div>
</div>      
{!! Form::hidden('search_lang', $informant->lang->id) !!}        
{!! Form::hidden('search_informant', $informant->id) !!}        
{!! Form::close() !!}
