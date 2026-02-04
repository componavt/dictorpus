@extends('layouts.page')

@section('page_title')
Исключение диалектных лемм из общего списка 
@stop

@section('body')    
    <p>Кликните на лемму, которая является диалектной.</p>
    @if ($by == 'sosd')
        @foreach ($lemmas as $sosd_title => $sosd_lemmas)
    <h2>{{ $sosd_title }}</h2> 
            @foreach ($sosd_lemmas as $lemma_id => $lemma)
    <p id='row-{{ $lemma_id }}'><span data-id='{{ $lemma_id }}' class='check-lemma'>{{ $lemma }}</span>
        <a style="text-decoration: none; font-size: 20px" href='{{ route('lemma.show', $lemma_id)}}' target='_blank'>→</a>
    </p>
            @endforeach
        @endforeach
    @else 
        @foreach ($lemmas as $lemma_id => $lemma)
    <p id='row-{{ $lemma_id }}'><span data-id='{{ $lemma_id }}' class='check-lemma'>{{ $lemma }}</span>
        <a style="text-decoration: none; font-size: 20px" href='{{ route('lemma.show', $lemma_id)}}' target='_blank'>→</a>
    </p>
        @endforeach
    
    @endif
@stop

@section('jqueryFunc')
    $('.check-lemma').on('click', function() {
        $(this).css('text-decoration','line-through');
        let id = $(this).data('id');
console.log('id: ' +id);    
        $.ajax({
            url: '/dict/lemma/'+id+'/set_dialectal', 
            type: 'GET',
            success: function(){       
                $("#row-"+id).hide();
            },
            error: function () {
                alert('Error');
            }
        }); 
        
    });
@stop


