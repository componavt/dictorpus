<p>
    <a href="{{ LaravelLocalization::localizeURL('/service/dict/synsets/').$args_by_get }}">
        {{ trans('messages.back_to_list') }}
    </a>
</p>

{!! Form::open(['id' => 'synset-create-form', 'method' => 'POST', 'route' => ['synset.store']]) !!}

<input type="hidden" name="lang_id" value="{{ $lang->id }}">

@include('dict.synset._header_fields')

@include('dict.synset._form_create_edit', ['submit_title' => trans('messages.create_new_m'), 'action' => 'create'])

{!! Form::close() !!}