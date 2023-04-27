<?php $link_style = 'style="text-decoration: none; color: '.($variant['right_frequency'] ? 'black' : 'red').';"';
      $align = 'style="text-align: '.($output=='frequency' ? 'right' : 'left').'"';
?>

            <td {{$align}}>
                <a href='/ru/corpus/sentence/results?search_dialect[]={{$dialect_id}}&search_words[1][w]="{{$template}}"'
                   {{ $link_style }} title="{{$dialect_info['name']}}">
                {{ $variant['t_'.$output] }}
                </a>
            </td>
            <td {{$align}}>
                {{ $variant['w_'.$output] }}
            </td>
