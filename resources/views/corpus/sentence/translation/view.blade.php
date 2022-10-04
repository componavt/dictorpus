       <div class="translation-view">
            <b>{{ trans('corpus.translation_in') }} {{ $lang_name }}</b><br>
              {!! process_text($translation->text) !!}
              <i class="fa fa-pencil-alt fa-lg translation-edit" title="{{trans('messages.edit')}}"
                 onClick="editTranslation({{$translation->sentence_id}}, {{$translation->w_id}}, {{$translation->lang_id}})"></i>
        </div>
