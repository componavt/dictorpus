    <!--p class='with-first-big-letter'><b>{{trans('dict.pos')}}:</b> {{$pos_name}}</p-->

    @include('widgets.form.formitem._select', 
            ['name' => 'choose-meaning',
             'values' => $meaning_values,
             'title' => trans('dict.meaning')]) 

    @if (sizeof($gramset_values)>1)
        @include('widgets.form.formitem._select', 
                ['name' => 'choose-gramset',
                 'values' => $gramset_values,
                 'title' => trans('dict.gramsets')]) 

    @endif             
