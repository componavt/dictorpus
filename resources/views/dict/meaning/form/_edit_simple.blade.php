<input id="meaning_text-{{ $meaning->id }}" name="meaning-{{ $meaning->id }}" type="text" value="{{ $meaning->getMeaningTextByLangCode('ru') }}">

<input type="button" class="btn btn-primary btn-default" value="{{trans('messages.save')}}" onclick="updateMeaning({{ $meaning->id }})">         
