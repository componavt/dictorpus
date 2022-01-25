<div id="translations">
    @foreach ($translations as $translation)
    <div class="row" id="translation_{{$translation->lang_id}}">
        @include('corpus.sentence.translation.view', ['lang_name'=>\App\Models\Dict\Lang::getNameById($translation->lang_id)])
    </div>
    @endforeach
</div>

@if (sizeof($lang_values))
<div class="row" id="add-translation-div">
  <div class="col-xs-2">
        @include('widgets.form.formitem._select',
            ['name' => 'lang_id_for_new',
             'values' =>$lang_values,
             'attributes' => ['id'=>'lang_id_for_new']])
    </div>
    <div class="col-xs-3">
        <button type="button" class="btn btn-info add-translation">
            {{trans('dict.add_translation')}}
        </button>
    </div>
</div>
@endif
