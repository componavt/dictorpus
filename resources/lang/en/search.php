<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Misc Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    
    'advanced_search'=> 'Advanced Search',
    'and' => 'AND',
    'choose_gram' => 'Choose grammatical attributes',
    'choose_pos' => 'Choose a part of speech',
    'founded_count' => '{0} There are no records.|{1} :count record was founded.|[2,Inf] :count records were founded.',
    'for_regex_search' => 'Templates (Regex and custom) are allowed in the field. You can use the following notation:
    <table class="help-list">
        <tr>
            <th>^</th>
            <td>beginning of string</td>
        </tr>
        <tr>
            <th>$</th>
            <td>end of string</td>
        </tr>
        <tr>
            <th>.</th>
            <td>any single character</td>
        </tr>
        <tr>
            <th>[…]</th>
            <td>any single character listed in the brackets. For example, <b>[akc]</b> matches any string <i>a</i>, <i>k</i>, <i>c</i></td>
        </tr>
        <tr>
            <th>[a-z]</th>
            <td>any Latin letter from a to z. And <b>[a-zA-Z]</b> matches any Latin letter regardless of case.</td>
        </tr>
        <tr>
            <th>[^…]</th>
            <td>any single character <b>except</b> listed in square brackets. For example, <b>[^ 0-9]</b> matches any character other than a digit.</td>
        </tr>
        <tr>
            <th>?</th>
            <td>the symbol preceding the question mark may or may not be encountered.</td>
        </tr>
        <tr>
            <th>*</th>
            <td>zero or more characters preceding the asterisk.</td>
        </tr>
        <tr>
            <th>+</th>
            <td>one or more characters preceding the plus.</td>
        </tr>
        <tr>
            <th>{n}</th>
            <td>n characters before parentheses. For example, <b>b{3}</b> matches the string <i>bbb</i></td>
        </tr>
        <tr>
            <th>{m,n}</th>
            <td>from m to n characters preceding the parentheses. For example, <b>a{1,3}</b> matches the lines <i>a</i>, <i>aa</i>, <i>aaa</i></td>
        </tr>
        <tr>
            <th>{m,}</th>
            <td>the previous character may occur m or more times.</td>
        </tr>
        <tr>
            <th>(...)</th>
            <td>parentheses specify the grouping of characters. For example, <b>(abc){1,3}</b> matches the strings <i>abc</i>, <i>abcabc</i>, <i>abcabcabc</i></td>
        </tr>
        <tr>
            <th>p1|p2</th>
            <td>p1 or p2. For example, <b>ab|cd</b> matches the strings <i>ab</i>, <i>cd</i></td>
        </tr>
        <tr>
            <th>V</th>
            <td>any vowel.</td>
        </tr>
        <tr>
            <th>С</th>
            <td>any consonant letter.</td>
        </tr>
    </table>',
    'for_text_fields' => 'For inexact search in the text fields use a <span class="warning">percent %</span> to replace any number of characters, a <span class="warning"> underscore _</span>  to replace one character.',
    'founded_entries' => '{0}|{1}, :count entry|[2,Inf], :count entries',
    'founded_texts' => '{0} There are no texts.|{1} :count text was founded.|[2,Inf] :count texts were founded.',
    'in_distance' => 'in the distance from :from to :to',
    'or' => 'OR',
    'refine_search' => 'Refine your search, please.',
    'show_by' => 'by',
    'simple_search'=> 'Simple Search',
    'year_from' => 'year (from)',
    'year_to' => 'year (to)',
];
