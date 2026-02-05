@extends('layouts.page')

@section('page_title')
Исключение диалектных лемм из общего списка 
@stop

@section('body')    
    <p>Кликните на лемму, которая является диалектной.</p>
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


