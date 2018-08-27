    <!--p class='with-first-big-letter'><b>{{trans('dict.pos')}}:</b> {{$pos_name}}</p-->

    @include('widgets.form._formitem_select', 
            ['name' => 'choose-meaning',
             'values' => $meaning_values,
             'title' => trans('dict.meaning')]) 

    @if (sizeof($gramset_values)>1)
        @include('widgets.form._formitem_select', 
                ['name' => 'choose-gramset',
                 'values' => $gramset_values,
                 'title' => trans('dict.gramsets')]) 

        @include('widgets.form._formitem_checkbox_group', 
                ['name' => 'choose-dialect',
                 'checked' => $dialect_value,
                 'values' => $dialect_values,
                 'title' => trans('dict.dialects')]) 
    @endif             
