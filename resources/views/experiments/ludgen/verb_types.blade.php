@extends('layouts.page')

@section('page_title')
{{ trans('navigation.experiments') }}
@endsection

@section('body')
    <h2>Типы глаголов Святозерского диалекта</h2>
    <table class='table-bordered table-wide table-striped rwd-table wide-md'>
    @foreach ($verbs as $o1 => $lemmas_o1)
        <tr style="vertical-align: top">
            <td style="text-align: right" rowspan="{{ $lemmas_o1['count'] }}">-{{ $o1 }}</td>
        <?php $count1=0; 
              ksort($lemmas_o1['words']);  ?>
        @foreach ($lemmas_o1['words'] as $o2 => $lemmas_o2)
            @if ($count1>0) 
        </tr>
        <tr style="vertical-align: top">
            @else <?php $count1++;?>
            @endif
            <td style="text-align: right" rowspan="{{ $lemmas_o2['count'] }}">-{{ $o2 }}</td>
            <?php $count2=0;  
                  ksort($lemmas_o2['words']);?>
            @foreach ($lemmas_o2['words'] as $o3 => $lemmas_o3)
                @if ($count2>0) 
        </tr>
        <tr style="vertical-align: top">
                @else <?php $count2++;?>
                @endif
            <td style="text-align: right" rowspan="{{ $lemmas_o3['count'] }}">-{{ $o3 }}</td>
                <?php $count3=0;  
                      ksort($lemmas_o3['words']);?>
                @foreach ($lemmas_o3['words'] as $o4 => $lemmas_o4)
                    @if ($count3>0) 
        </tr>
        <tr style="vertical-align: top">
                    @else <?php $count3++;?>
                    @endif
            <td style="text-align: right" rowspan="{{ sizeof($lemmas_o4['words']) }}">-{{ $o4 }}</td>
                    <?php $count4=0;  
                          asort($lemmas_o4['words']);?>
                    @foreach ($lemmas_o4['words'] as $lemma_id => $lemma)
                        @if ($count4>0) 
        </tr>
        <tr style="vertical-align: top">
                        @else <?php $count4++;?>
                        @endif
            <td><a href="{{ route('lemma.show', $lemma_id) }}">{{ $lemma }}</a></td>
                    @endforeach
                @endforeach
            @endforeach            
        @endforeach
        </tr>
    @endforeach
    </table>
        
@endsection
