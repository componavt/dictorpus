{!! Form::model($sentence, array(
    'id' => 'change-sentence-'.$sentence->id,
    'method' => 'PUT',
    'route' => array('sentence.update', $sentence->id),
    'onsubmit' => 'return saveSentence('.(int) $sentence->id.');'
)) !!}
<input type="hidden" name="original_xml_hash" value="{{ hash('sha256', (string) $sentence->text_xml) }}">
@include('widgets.form.formitem._textarea',
        ['name' => 'text_xml',
         'attributes' => ['id'=>'text_xml', 'rows'=>5],
         'special_symbol' => true])
<input class="btn btn-primary btn-default" type="submit" value="{{trans('messages.save')}}">

{!! Form::close() !!}
