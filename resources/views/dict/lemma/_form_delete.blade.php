                    <!--button class="btn btn-danger btn-xs btn-delete delete-link" value="{{$lemma->id}}">Delete</button-->
                    {!! Form::model($lemma, ['method' => 'delete', 'route' => ['lemma.destroy', $lemma->id], 'class' =>'form-inline form-delete']) !!}
                    {!! Form::hidden('id', $lemma->id) !!}
                    {!! Form::submit(trans('messages.delete'), ['class' => 'btn btn-xs btn-danger delete']) !!}
                    {!! Form::close() !!}
{{-- {{--                   {!! Form::open(['method' => 'DELETE', 'route' => ['lemma.destroy', $lemma->id], 'class' => 'delete-link form-inline']) !!}
                    @include('widgets.form._formitem_btn_submit', ['title' => trans('messages.delete'), 'class'=>"btn btn-danger btn-xs btn-delete delete-link"])
                    {!! Form::close() !!} --}}
