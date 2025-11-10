        <h2>{{ trans('messages.editing')}} {{ trans('corpus.of_monument')}} <span class='imp'>"{{ $monument->title}}"</span></h2>
        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/monument/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a>
            | <a href="{{ LaravelLocalization::localizeURL('/corpus/monument/'.$monument->id) }}{{$args_by_get}}">{{ trans('messages.back_to_show') }}</a></p>
        
        {!! Form::model($monument, array('method'=>'PUT', 'route' => array('monument.update', $monument->id))) !!}
        @include('corpus.monument._form_create_edit', ['submit_title' => trans('messages.save'),
                                      'action' => 'edit'])
        {!! Form::close() !!}
