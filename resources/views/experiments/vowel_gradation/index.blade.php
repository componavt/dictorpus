@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<h2>Поиск закономерностей в чередовании гласных</h2>
<p>Словоформы имен ливвиковского младописьменного варианта, которые:</p>
<ol>
    @for ($i=1; $i<3; $i++)
    <li>в форме номинатива ед. заканчиваются на {{$u[$i]}} или {{$a[$i]}}, при этом в форме генитива ед. заканчиваются на {{$a[$i]}}n</li>
    <ul>
        @foreach ($pos_list as $pos_code => $pos_name)
        <li>{{$pos_name}}</li>
        <ul>
            @for ($c=2; $c<5; $c++)
            <li>{{$c}}-сложные</li>
            <ul>
                @foreach ($parts as $p=>$p_gr)
                <li><a href="/experiments/vowel_gradation/nom_gen_part/{{$i}}/{{$pos_code}}/{{$c}}/{{$p}}">{{$p==1 ? $p_gr[$i] : $p_gr}}</a></li>
                @endforeach
            </ul>
            @endfor
        </ul>
        @endforeach
    </ul>
    @endfor
</ol>
@endsection