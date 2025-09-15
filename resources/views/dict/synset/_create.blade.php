    <p><a href="{{ LaravelLocalization::localizeURL('/service/dict/synsets/') }}">{{ trans('messages.back_to_list') }}</a></p>

    <div class='row' style='margin-bottom: 20px;'>
        <div class='col-sm-6'><b>Язык:</b> {{ $lang->name }}</div>
        <div class='col-sm-6'>
        @include('widgets.form.formitem._select',
                ['name' => 'pos_id',
                 'title'=> 'часть&nbsp;речи:&nbsp;',
                 'is_flex' => true,
                 'values' => $pos_values])
        </div>
    </div>
        
    {!! Form::open(array('method'=>'POST', 'route' => array('synset.store'))) !!}
    <input type="hidden" name="lang_id" value="{{ $lang->id }}">    
    @include('dict.synset._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                  'action' => 'create'])
    {!! Form::close() !!}
