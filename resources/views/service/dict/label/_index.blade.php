@foreach ($meaning->labels()->where('visible',1)->get() as $label)
    {{ $label->short }}
    <i class="fa fa-times fa-lg clickable" title="удалить метку" style="height:8px; width: 8px;"
       onClick="removeVisibleLabel({{ $meaning->id }}, {{ $label->id }})"></i>
@endforeach
