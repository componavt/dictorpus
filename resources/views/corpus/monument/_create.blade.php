        <p>
            <a href="{{ LaravelLocalization::localizeURL('/corpus/monument/') }}{{$args_by_get}}">{{ trans('messages.back_to_list') }}</a></p>
        
        {!! Form::open(array('method'=>'POST', 'route' => array('monument.store'))) !!}
        @include('corpus.monument._form_create_edit', ['submit_title' => trans('messages.create_new_m'),
                                      'action' => 'create',
                                      'monument' => null])
        {!! Form::close() !!}
