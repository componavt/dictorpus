<i>{{$example_obj->example}}</i> {{$example_obj->example_ru}}
@if (!isset($access_edition) || $access_edition)
    <i class="fa fa-pencil-alt fa-lg clickable blue-color" onClick="editExample({{$example_obj->id}})"></i>
@endif