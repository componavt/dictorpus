        {{-- EVENT --}}
        <?php if ($action=='create') { $informant_value = NULL; } ?>        
        @include('widgets.form.formitem._select2', 
                ['name' => 'event.informants', 
                 'values' =>$informant_values,
                 'value' => $informant_value,
                 'call_add_onClick' => 'addInformant()',
                 'call_add_title' => trans('messages.create_new_m'),
                 'title' => trans('corpus.informant')]) 
                 
        <?php $event_date_value = ($action=='edit' && $text->event) ? ($text->event->date) : NULL; ?>
        @include('widgets.form.formitem._text', 
                ['name' => 'event.date', 
                 'value' => $event_date_value,
                 'size' => 4,
                 'title'=>trans('corpus.record_year')])
                 
        <?php $event_place_value = ($action=='edit' && $text->event) ? ($text->event->place_id) : NULL; ?>
        @include('widgets.form.formitem._select', 
                ['name' => 'event.place_id', 
                 'values' =>$place_values,
                 'value' => $event_place_value,
                 'call_add_onClick' => "addPlace('event_place_id')",
                 'call_add_title' => trans('messages.create_new_g'),
                 'title' => trans('corpus.record_place')]) 
                 
        <?php if ($action=='create') { $recorder_value = NULL; } ?>        
        @include('widgets.form.formitem._select2',
                ['name' => 'event.recorders', 
                 'values' =>$recorder_values,
                 'value' => $text? $text->recorderValue() : [],
                 'call_add_onClick' => 'addRecorder()',
                 'call_add_title' => trans('messages.create_new_m'),
                 'title' => trans('corpus.recorded'),
                 'class'=>'multiple-select form-control'
            ])
