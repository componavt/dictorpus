@foreach ($meaning->labels()->where('visible',1)->get() as $label)
    {{ $label->short }}
    <i class="fa fa-times fa-lg clickable" title="удалить метку"
       onClick="removeVisibleLabel({{ $meaning->id }}, {{ $label->id }})"></i>
@endforeach

<i class="fa fa-plus fa-lg clickable link-color" 
    title="Добавить метку"
    onClick="addLabel({{ $meaning->id }})"></i>
