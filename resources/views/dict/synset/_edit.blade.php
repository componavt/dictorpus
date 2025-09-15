    <p><a href="{{ LaravelLocalization::localizeURL('/service/dict/synsets/') }}">{{ trans('messages.back_to_list') }}</a></p>
    <h2>{{ trans('messages.editing')}} {{ trans('dict.of_synsets')}} <span class='imp'>{{ $synset->name}}</span></h2>
    
    <div class='row' style='margin-bottom: 20px;'>
        <div class='col-sm-6'><b>Язык:</b> {{ $synset->lang->name }}</div>
        <div class='col-sm-6'>
        @include('widgets.form.formitem._select',
                ['name' => 'pos_id',
                 'values' =>$pos_values,
                 'value' => $synset->pos_id,
                 'title'=> 'часть&nbsp;речи:&nbsp;',
                 'is_flex' => true,
                 'attributes' => ['disabled'=>true]])
        </div>
    </div>
    
    {!! Form::model($synset, array('method'=>'PUT', 'route' => array('synset.update', $synset->id))) !!}
    @include('dict.synset._form_create_edit', ['submit_title' => trans('messages.save'),
                                  'action' => 'edit'])
    {!! Form::close() !!}
