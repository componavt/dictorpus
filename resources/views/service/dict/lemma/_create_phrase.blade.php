<div id='new-phrase-{{$meaning_id}}'>
@include('service.dict.lemma._create_edit_phrase', ['phrase_id'=>'new-for-'.$meaning_id, 'func'=>'createPhrase('.$meaning_id.')'] )
</div>
