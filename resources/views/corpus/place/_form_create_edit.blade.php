<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('corpus.name').' ('.\App\Models\Dict\Lang::getNameByCode('ru'). ' '. trans('dict.lang').')'])
                                  
        @include('widgets.form.formitem._select', 
                ['name' => 'region_id', 
                 'values' =>$region_values,
                 'title' => trans('corpus.region')]) 
                 
        @include('widgets.form.formitem._select', 
                ['name' => 'district_id', 
                 'values' =>$district_values,
                 'title' => trans('corpus.district')]) 
                 
        @include('widgets.form.formitem._select',
                ['name' => 'lang_id',
                 'values' =>$lang_values,
                 'is_multiple' => true,
                 'title' => trans('dict.lang'),
                 'attributes' => ['id'=>'lemma_lang_id']])
                 
        @include('widgets.form.formitem._select2',
                ['name' => 'dialects', 
                 'values' =>$dialect_values,
                 'value' => $dialect_value,
                 'title' => trans('navigation.dialects'),
                 'class'=>'select-dialect form-control'])
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('corpus.name').' ('.\App\Models\Dict\Lang::getNameByCode('en'). ' '. trans('dict.lang').')'])
                 
        @foreach ($lang_values as $lang_id => $lang_n) 
            @if ($lang_id)
                @include('widgets.form.formitem._text', 
                        ['name' => 'other_names['.$lang_id.']', 
                         'special_symbol' => true,
                         'value' => $other_names[$lang_id] ?? NULL,
                         'title'=>trans('corpus.name').' ('.$lang_n.')'])    
            @endif
        @endforeach
    </div>
</div>                 
