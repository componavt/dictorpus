        @include('widgets.form.formitem._select', 
                ['name' => 'region_id', 
                 'values' =>$region_values,
                 'title' => trans('corpus.region')]) 
                 
        @include('widgets.form.formitem._select', 
                ['name' => 'district_id', 
                 'values' =>$district_values,
                 'title' => trans('corpus.district')]) 
                 
        @include('widgets.form.formitem._text', 
                ['name' => 'name_ru', 
                 'title'=>trans('corpus.name').' ('.\App\Models\Dict\Lang::getNameByCode('ru'). ' '. trans('dict.lang').')'])
                                  
        @include('widgets.form.formitem._text', 
                ['name' => 'name_en', 
                 'title'=>trans('corpus.name').' ('.\App\Models\Dict\Lang::getNameByCode('en'). ' '. trans('dict.lang').')'])
                 
<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'latitude', 
                 'title'=>trans('corpus.latitude')])
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._text', 
                ['name' => 'longitude', 
                 'title'=>trans('corpus.longitude')])
    </div>
</div>                 
        <input type="hidden" id="parent_place" value="birth_place_id">         
