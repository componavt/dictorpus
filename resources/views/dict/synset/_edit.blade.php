@include('dict.synset._nav', ['active' => 'edit'])

<h2>{{ trans('messages.editing')}} {{ trans('dict.of_synsets')}} <span class='imp'>{{ $synset->name}}</span></h2>

{!! Form::model($synset, ['id' => 'synset-edit-form', 'method' => 'PUT', 'route' => ['synset.update', $synset->id]]) !!}
@include('dict.synset._header_fields')

@include('dict.synset._form_create_edit', ['submit_title' => trans('messages.save'),
'action' => 'edit'])
{!! Form::close() !!}
<hr>

@include('dict.synset._collocations')