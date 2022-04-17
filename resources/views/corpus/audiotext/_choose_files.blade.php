<div class='row'>
@foreach ($audio_values as $filename)
    <div class='col-sm-3'>
            @include('widgets.form.formitem._checkbox',
                    ['name' => 'filenames[]',
                     'title' => '',
                    'value' => $filename,
                    'tail'=>$filename]) 
    </div>
@endforeach
</div>

