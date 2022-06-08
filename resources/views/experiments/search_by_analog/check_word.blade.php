@extends('layouts.page')

@section('page_title')
    Проверка слова при поиске
@stop

@section('body')     
    <h2>{{$property=='gramset' ? 'грамсета' : 'части речи'}}</h2>
    @foreach ($wordforms as $wordform)
        <p><b>{{$wordform->wordform}}</b><br>
            {{$property}}: <?php print $wordform->{$property_id}; ?>
        </p>
        <p>
            по конечным буквосочетаниям<br>
            ending: <b>{{$wordform->ending}}</b><br>
            <?php $list = \App\Library\Experiments\SearchByAnalog::searchPosGramsetByEnding($search_lang, $wordform->wordform, $wordform->ending, $table_name, $property_id);
                list($eval, $winner) = \App\Library\Experiments\SearchByAnalog::getEvalForOneValue($list, $wordform->{$property_id});
            ?>
            list: {!!\App\Library\Str::arrayToString($list)!!}<br>
            predicted: <b>{{$winner}}</b><br>                
            evaluation: <b>{{$eval}}</b>
        </p>
        
        @if ($property=='gramset')
        <p>
            по самым длинным псевдоокончаниям<br>
            affix: <b>{{$wordform->affix}}</b><br>
            <?php $list = \App\Library\Experiments\SearchByAnalog::searchGramsetByAffix($wordform, $search_lang);
                list($eval, $winner) = \App\Library\Experiments\SearchByAnalog::getEvalForOneValue($list, $wordform->{$property_id});
            ?>
            list: {!!\App\Library\Str::arrayToString($list)!!}<br>
            predicted: <b>{{$winner}}</b><br>                
            evaluation: <b>{{$eval}}</b>
        </p>
        
        <p>
            по всем псевдоокончаниям<br>
            affix: <b>{{$wordform->affix}}</b><br>
            <?php 
                $lists = \App\Library\Experiments\SearchByAnalog::searchGramsetByAffixesAllLists($wordform, $search_lang);
                $last_list = end($lists);
                list($eval, $winner) = \App\Library\Experiments\SearchByAnalog::getEvalForOneValue($last_list, $wordform->{$property_id});
            ?>
            lists: 
            @foreach ($lists as $affix => $list) 
                <dd>{{$affix}}</dd>
                <dd>{!!\App\Library\Str::arrayToString($list)!!}</dd><br>
            @endforeach
            predicted: <b>{{$winner}}</b><br>                
            evaluation: <b>{{$eval}}</b>
        </p>
        @endif
        <br><br>
    @endforeach
@stop


