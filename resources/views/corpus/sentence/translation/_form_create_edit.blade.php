    <div class="col-sm-10">        
    @include('widgets.form.formitem._textarea', 
            ['name' => 'translations_for_'.$lang_id,
             'value' => $translation_text ?? '',
             'title' => trans('corpus.translation_in').' '.$lang_name,
             'attributes' => ['rows'=>2] ])
    </div>
    <div class="col-sm-2"><br><br>  
        <input class="btn btn-primary btn-default" type="submit" value="{{trans('messages.save')}}" onClick="saveTranslation({{$sentence_id}}, {{$lang_id}}, '{{$action}}')">        
    </div>