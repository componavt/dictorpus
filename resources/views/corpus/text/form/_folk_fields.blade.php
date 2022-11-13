<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._select2',
                ['name' => 'cycles', 
                 'values' =>$cycle_values,
                 'value' => $cycle_value ?? null,
                 'title' => trans('navigation.cycles'),
                 'class'=>'multiple-select-cycle form-control'
            ])
    </div>
    <div class="col-sm-6">
        @include('widgets.form.formitem._select2',
                ['name' => 'motives', 
                 'values' =>$motive_values,
                 'value' => $text? $text->motiveValue() : [],
                 'title' => trans('navigation.motives'),
                 'class'=>'multiple-select-motive form-control'
            ])
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        @include('widgets.form.formitem._select2',
                ['name' => 'plots', 
                 'values' =>$plot_values,
                 'value' => $plot_value ?? null,
                 'title' => trans('navigation.plots'),
                 'class'=>'multiple-select-plot form-control'
            ])
    </div>
    <div class="col-sm-6"><b>{{trans('navigation.topics')}}</b>
    <?php $topic_count = 0; ?>
        @if (isset($topic_value) && $topic_value) 
            @foreach ($topic_value as $topic_id =>$topic_num) 
        <div class='row'>
            <div class='col-sm-2'>
        @include('widgets.form.formitem._text', 
                ['name' => 'topics['.$topic_count.'][sequence_number]', 
                 'value'=> $topic_num])
            </div>
            <div class='col-sm-10'>
        @include('widgets.form.formitem._select2',
                ['name' => 'topics['.$topic_count++.'][topic_id]', 
                 'values' =>$topic_values,
                 'value' => [$topic_id],
                 'is_multiple' => false,
                 'class'=>'select-topic form-control'
            ])
{{--                 'call_add_onClick' => "addTopic()",--}}
            </div>
        </div>
            @endforeach
        @endif
        @for ($i=0; $i<3; $i++) 
        <div class='row'>
            <div class='col-sm-2'>
        @include('widgets.form.formitem._text', 
                ['name' => 'topics['.$topic_count.'][sequence_number]'])
            </div>
            <div class='col-sm-10'>
        @include('widgets.form.formitem._select2',
                ['name' => 'topics['.$topic_count++.'][topic_id]', 
                 'values' =>$topic_values,
                 'value' => [],
                 'is_multiple' => false,
                 'class'=>'select-topic form-control'
            ])
{{--                 'call_add_onClick' => "addTopic()",--}}
            </div>
        </div>
        @endfor
    </div>
</div>                 
