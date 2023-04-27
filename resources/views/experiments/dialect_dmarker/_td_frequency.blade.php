<?php $align = 'style="text-align: '.($output=='frequency' ? 'right' : 'left').'"';?>

            <td {{$align}}>
                <a href='/ru/corpus/sentence/results?search_dialect[]={{$dialect_id}}&search_words[1][w]="{{$template}}"'
                   class="{{ $variant['class'] }}" title="{{$dialect_info['name']}}">
                {{ $variant['t_'.$output] }}
                </a>
            </td>
            <td {{$align}}>
                {{ $variant['w_'.$output] }}
            </td>
