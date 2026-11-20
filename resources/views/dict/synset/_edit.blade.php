    <p><a href="{{ LaravelLocalization::localizeURL('/service/dict/synsets/').$args_by_get }}">{{ trans('messages.back_to_list') }}</a></p>
    <h2>{{ trans('messages.editing')}} {{ trans('dict.of_synsets')}} <span class='imp'>{{ $synset->name}}</span></h2>
    
    {!! Form::model($synset, ['id' => 'synset-edit-form', 'method' => 'PUT', 'route' => ['synset.update', $synset->id]]) !!}
    @include('dict.synset._header_fields')
    
    @include('dict.synset._form_create_edit', ['submit_title' => trans('messages.save'),
                                  'action' => 'edit'])
    {!! Form::close() !!}
<hr>

<p><a class="btn btn-default" href="{{ LaravelLocalization::localizeURL('/dict/synset/' . $synset->id . '/examples/candidates') . $args_by_get }}">
        Корпусные кандидаты всех участников
    </a></p>

@include('dict.synset._collocations')