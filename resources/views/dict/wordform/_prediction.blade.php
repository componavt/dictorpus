<h3>Возможно это один из следующих вариантов?</h3>
@foreach ($exist_lemmas as $id=>$lemma) 
<p>
    <b><a href="/dict/lemma/{{$id}}">{{$lemma['lemma']}}</a></b>, {{$lemma['pos']}}, {{$lemma['gramset']}} ({{$lemma['proc']}})
    @foreach ($lemma['meanings'] ?? [] as $m_id => $meaning)
    <br><input type='radio' name='prediction' value='{{$m_id}}' onClick="fillInterpretation('{{$meaning}}')"> {{$meaning}}
    @endforeach
</p>
@endforeach       
@foreach ($prediction as $id=>$lemma) 
<input type='radio' name='prediction' value='{{$id}}'>
<b>{{$lemma['lemma']}}</b>, {{$lemma['pos']}}, {{$lemma['gramset']}} ({{$lemma['proc']}})
<br>                 
@endforeach                     
<br>
@include('widgets.form.formitem._text', 
        ['name' => 'interpretation',
         'special_symbol' => true,
         'title' => trans('dict.prediction_interpretation')]) 
