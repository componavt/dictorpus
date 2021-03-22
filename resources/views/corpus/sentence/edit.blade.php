{!! Form::model($sentence, array('id'=>'change-sentence-'.$sentence->id, 'method'=>'PUT', 'route' => array('sentence.update', $sentence->id))) !!} 
@include('widgets.form.formitem._textarea', 
        ['name' => 'text_xml', 
         'attributes' => ['id'=>'text_xml', 'rows'=>5],
         'special_symbol' => true])
<input onClick="saveSentence({{$sentence->id}})" class="btn btn-primary btn-default" type="submit" value="{{trans('messages.save')}}">

{!! Form::close() !!}
