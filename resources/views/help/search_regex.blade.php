{{trans('search.regex_title')}}

    <table class="help-list">
        <tr>
            <th>^</th>
            <td>{{trans('search.regex_begin')}}</td>
        </tr>
        <tr>
            <th>$</th>
            <td>{{trans('search.regex_end')}}</td>
        </tr>
        <tr>
            <th>.</th>
            <td>{{trans('search.regex_point')}}</td>
        </tr>
        <tr>
            <th>[…]</th>
            <td>{!!trans('search.regex_brackets')!!}</td>
        </tr>
        <tr>
            <th>[a-z]</th>
            <td>{!!trans('search.regex_range')!!}</td>
        </tr>
        <tr>
            <th>[^…]</th>
            <td>{!!trans('search.regex_except')!!}</td>
        </tr>
        <tr>
            <th>?</th>
            <td>{{trans('search.regex_question')}}</td>
        </tr>
        <tr>
            <th>*</th>
            <td>{{trans('search.regex_asterisk')}}</td>
        </tr>
        <tr>
            <th>+</th>
            <td>{{trans('search.regex_plus')}}</td>
        </tr>
        <tr>
            <th>{n}</th>
            <td>{!!trans('search.regex_parentheses')!!}</td>
        </tr>
        <tr>
            <th>{m,n}</th>
            <td>{!!trans('search.regex_parentheses_from_to')!!}</td>
        </tr>
        <tr>
            <th>{m,}</th>
            <td>{{trans('search.regex_parentheses_from')}}</td>
        </tr>
        <tr>
            <th>(...)</th>
            <td>{!!trans('search.regex_grouping')!!}</td>
        </tr>
        <tr>
            <th>p1|p2</th>
            <td>{!!trans('search.regex_or')!!}</td>
        </tr>
@if (isset($with_custom) && $with_custom)        
        <tr>
            <th>V</th>
            <td>{{trans('search.regex_vowel')}}</td>
        </tr>
        <tr>
            <th>С</th>
            <td>{{trans('search.regex_consonant')}}</td>
        </tr>
@endif        
    </table>