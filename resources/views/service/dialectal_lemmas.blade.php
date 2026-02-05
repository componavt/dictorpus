@extends('layouts.page')

@section('page_title')
{{ $by == 'dial' ? 'Диалектные' : 'Нормированные' }} леммы (язык {{ $lang->name }})
@stop

@section('headExtra')
<style>
    .check-lemma:hover {
        color: red;
    }
</style>
@stop

@section('body')    
    <p>
        @if ($by != 'dial')
        Это не полный список нормированных лемм. Это - кандидаты в диалектные леммы. Из списка исключены леммы, имеющие не меньше 13 словоформ нормированного варианта.
        @endif
        Чтобы исключить {{ $by == 'dial' ? 'нормированную' : 'диалектную' }} лемму из общего списка, кликните на неё. Лемма зачеркнётся, потом исчезнет из этого списка.</p>
    <OL>
    @if ($by == 'sosd')
        @foreach ($lemmas as $sosd_title => $sosd_lemmas)
    <h2>{{ $sosd_title }}</h2> 
            @include('service.dialectal_lemmas_in', ['lemmas'=>$sosd_lemmas])
        @endforeach
    @else 
        @include('service.dialectal_lemmas_in')    
    @endif
    </ol>
@stop

@section('jqueryFunc')
    $('.check-lemma').on('click', function() {
        $(this).css('text-decoration','line-through');
        let id = $(this).data('id');
//console.log('id: ' +id);    
        $.ajax({
            url: '/dict/lemma/'+id+'/set_dialectal', 
            data: {is_norm : {{ $by == 'dial' ? 1 : 0 }}},
            type: 'GET',
            success: function(){       
                $(".row-"+id).hide();
            },
            error: function () {
                alert('Error');
            }
        }); 
        
    });
@stop


