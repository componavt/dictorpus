@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<h2>Поиск закономерностей в чередовании гласных</h2>
<p>Словоформы имен ливвиковского младописьменного варианта, которые:</p>
<ol>
    @for ($i=1; $i<3; $i++)
    <li>в форме номинатива ед.ч. заканчиваются на {{$u[$i]}} или {{$a[$i]}}, при этом в форме генитива ед.ч. заканчиваются на {{$a[$i]}}n
    <ul>
        @foreach ($pos_list as $pos_code => $pos_name)
        <li>{{$pos_name}}
        <ul>
            @for ($c=2; $c<5; $c++)
            <li>{{$c}}-сложные</li>
            <ul>
                @foreach ($parts as $p=>$p_gr)
                <li><a href="/experiments/vowel_gradation/nom_gen_part/{{$i}}/{{$pos_code}}/{{$c}}/{{$p}}">{{$p==1 ? $p_gr[$i] : $p_gr}}</a></li>
                @endforeach
            </ul>
            @endfor
        </ul></li>
        @endforeach
    </ul></li>
    @endfor
    @for ($i=1; $i<3; $i++)
    <li>в форме номинатива ед.ч. заканчиваются на согласную, при этом в форме генитива ед.ч. заканчиваются на {{$a[$i]}}n
    <ul>
        @foreach ($pos_list as $pos_code => $pos_name)
        <li>{{$pos_name}}
        <ul>
            @for ($p=1; $p<3; $p++)
            <li><a href="/experiments/vowel_gradation/nom_gen_part/{{$i+2}}/{{$pos_code}}/0/{{$p}}">{{$p==1 ? $parts[$p][$i] : $parts[$p]}}</a></li>
            @endfor
        </ul></li>
        @endforeach
    </ul></li>
    @endfor
</ol>
@endsection