<div class="row" id="fragment">
    <div class="col-sm-10">        
    @include('widgets.form.formitem._textarea', 
            ['name' => 'fragment_text',
             'value' => $fragment_text ?? '',
             'title' => trans('corpus.fragment'),
             'attributes' => ['rows'=>2] ])
    </div>
    <div class="col-sm-2"><br><br>  
        <input class="btn btn-primary btn-default" type="submit" value="{{trans('messages.save')}}" onClick="saveFragment({{$sentence_id}}, {{$w_id}})">        
    </div>
</div>