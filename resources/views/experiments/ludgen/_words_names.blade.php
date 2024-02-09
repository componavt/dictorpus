<table class='table-bordered table-wide table-striped rwd-table wide-md'>
    <tr>
@foreach ($lemmas as $lemma)
<td><a href="{{ route('lemma.show',$lemma->id) }}">{{ $lemma->lemma}}</a></td>
@endforeach
    </tr>
</table>