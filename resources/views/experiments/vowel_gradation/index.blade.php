@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
<h2>Поиск закономерностей в чередовании гласных</h2>
<p>Словоформы имен (существительные и прилагательные) ливвиковского младописьменного варианта, которые:<br>
    1) <a href="/experiments/vowel_gradation/nom_gen_part/1">в форме номинатива ед. заканчиваются на u или a, при этом в форме генитива ед. заканчиваются на an</a><br>
    2) <a href="/experiments/vowel_gradation/nom_gen_part/2">в форме номинатива ед. заканчиваются на y или ä, при этом в форме генитива ед. заканчиваются на än</a></p>
@endsection