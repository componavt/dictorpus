<div id='meaning-{{$meaning->id}}'>
    <i class="fa fa-times fa-lg clickable" data-delete="{{csrf_token()}}" onClick="removeLabelMeaning(this, {{$meaning->id}}, {{$label_id}}, '{{$meaning->getMultilangMeaningTextsString('ru')}}')"></i>
    {{$meaning->getMultilangMeaningTextsString('ru')}}
@foreach ($meaning->examples as $example) 
    @include('dict.example.view', ['meaning_id'=>$meaning->id, 'example_obj'=>$example])
@endforeach
    <i id="add-example-for-{{$meaning->id}}" class="fa fa-plus fa-lg clickable link-color" onClick="addSimpleExample({{$meaning->id}})"></i>                        
</div>

