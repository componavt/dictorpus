<div class="row">
    <div class="col-sm-6">
        @include('widgets.form._formitem_text', 
                ['name' => 'name_en', 
                 'title'=>trans('corpus.name').' ('.\App\Models\Dict\Lang::getNameByCode('en'). ' '. trans('dict.lang').')'])
                 
        @include('widgets.form._formitem_text', 
                ['name' => 'name_ru', 
                 'title'=>trans('corpus.name').' ('.\App\Models\Dict\Lang::getNameByCode('ru'). ' '. trans('dict.lang').')'])
                                  
        @include('widgets.form._formitem_select', 
                ['name' => 'district_id', 
                 'values' =>$district_values,
                 'title' => trans('corpus.district')]) 
                 
        @include('widgets.form._formitem_select', 
                ['name' => 'region_id', 
                 'values' =>$region_values,
                 'title' => trans('corpus.region')]) 
    </div>
    <div class="col-sm-6">
        @foreach ($lang_values as $lang_id => $lang_n) 
            <?php $other_name = isset($other_names[$lang_id]) ? $other_names[$lang_id] : NULL; ?>
            @include('widgets.form._formitem_text', 
                    ['name' => 'other_names['.$lang_id.']', 
                     'special_symbol' => true,
                     'value' => $other_name,
                     'title'=>trans('corpus.name').' ('.$lang_n.')'])            
        @endforeach
    </div>
</div>                 

@include('widgets.form._formitem_btn_submit', ['title' => $submit_title])
