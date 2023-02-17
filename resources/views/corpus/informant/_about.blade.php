    <h2>{{$informant->name}}</h2>
    
    @if ($informant->birth_place)
    <p><b>{{trans('corpus.birth_place')}}</b>: {{$informant->birth_place->placeString('', false)}}</p>
    @endif
    
    @if ($informant->lang)
    <p><b>{{trans('dict.lang')}}</b>: {{$informant->lang->name}}</p>
    @endif
    
    @if ($informant->dialect_name)
    <p><b>{{trans('dict.dialect')}}</b>: {{$informant->dialect_name}}</p>
    @endif
    



