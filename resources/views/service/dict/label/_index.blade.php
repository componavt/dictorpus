@foreach ($meaning->labels()->where('visible',1)->get() as $label)
    {{ $label->short }}
    <i class="fa fa-times fa-lg clickable" title="удалить метку" style="height:8px; width: 8px;"
       onClick="removeVisibleLabel({{ $meaning->id }}, {{ $label->id }})"></i>
@endforeach
<i class="fa fa-plus fa-lg clickable link-color" style="height:8px; width: 8px;" 
   title="Добавить метку"
   onClick="addLabel({{ $meaning->id }})"></i>
