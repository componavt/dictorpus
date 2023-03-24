<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Misc Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */
    
    'add_left_context' => 'add left context',
    'add_right_context' => 'add right context',
    'add_word' => 'add another word to the search',
    'advanced_search'=> 'Advanced Search',
    'and' => 'AND',
    'author_or_trans' => 'Author',
    'choose_gram' => 'Choose grammatical attributes',
    'choose_pos' => 'Choose a part of speech',
    'd_f' => 'from',
    'd_t' => 'to',
    'distance' => 'Distance',
    'found_count' => '{0} There are no records.|{1} :count record was found.|[2,Inf] :count records were found.',
    'found_lemmas' => '{0} There are no lemmas.|{1} :count lemma was found.|[2,Inf] :count lemmas were found.',
    'found_texts' => '{0} There are no texts.|{1} :count text was found.|[2,Inf] :count texts were found.',
    'for_text_fields' => 'For inexact search in the text fields use a <span class="warning">percent %</span> to replace any number of characters, a <span class="warning"> underscore _</span>  to replace one character.',
    'found_entries' => '{0}|{1}, :count entry|[2,Inf], :count entries',
    'found_sentences' => '{0}|{1}, :count sentence|[2,Inf], :count sentences',
    'found_texts' => '{0} There are no texts|{1} :count text was found|[2,Inf] :count texts were found',
    'in_distance' => 'in the distance from :from to :to',
    
    'or' => 'OR',
    'other_search' => 'You can use other types of search',
    
    'regex_title' => 'Templates (Regex and custom) are allowed in the field. You can use the following notation:',
    'regex_begin' => 'beginning of string',
    'regex_end' => 'end of string',
    'regex_point' => 'any single character',
    'regex_brackets' => 'any single character listed in the brackets. For example, <b>[akc]</b> matches any string <i>a</i>, <i>k</i>, <i>c</i>',
    'regex_range' => 'any Latin letter from a to z. And <b>[a-zA-Z]</b> matches any Latin letter regardless of case.',
    'regex_except' => 'any single character <b>except</b> listed in square brackets. For example, <b>[^ 0-9]</b> matches any character other than a digit.',
    'regex_question' => 'the symbol preceding the question mark may or may not be encountered.',
    'regex_asterisk' => 'zero or more characters preceding the asterisk.',
    'regex_plus' => 'one or more characters preceding the plus.',
    'regex_parentheses' => 'n characters before parentheses. For example, <b>b{3}</b> matches the string <i>bbb</i>',
    'regex_parentheses_from_to' => 'from m to n characters preceding the parentheses. For example, <b>a{1,3}</b> matches the lines <i>a</i>, <i>aa</i>, <i>aaa</i>',
    'regex_parentheses_from' => 'the previous character may occur m or more times.',
    'regex_grouping' => 'parentheses specify the grouping of characters. For example, <b>(abc){1,3}</b> matches the strings <i>abc</i>, <i>abcabc</i>, <i>abcabcabc</i>',
    'regex_or' => 'p1 or p2. For example, <b>ab|cd</b> matches the strings <i>ab</i>, <i>cd</i>',
    'regex_vowel' => 'any vowel.',
    'regex_consonant' => 'any consonant letter.',
    'refine_search' => 'Refine your request',
    'search_results' => 'Search results',
    'search_simple_lemma' => '<p>The search is performed by any form of a word or by interpretation in any language (Vepsian, Karelian, Russian, English or Finnish).</p>',
    'search_simple_text' => '<p>The search is based on texts and translations into Russian.',
    'search_simple_title' => 'Dictionary and corpus simple search',
    'search_simple_title_by_corpus' => 'Corpus simple search',
    'search_simple_title_by_dict' => 'Dictionary simple search',
    'search_simple_word' => '<p>This field can contain any word (or fragment of a word) in any language (Vepsian, Karelian, Russian, English or Finnish).</p> '
                           . '<p>This is a normal substring search, so no wildcards needed.</p>'
                           . '<p>All dictionary entries and texts in the corpus where this word occurs will be found.</p>',
    'show_by' => 'by',
    'simple_search'=> 'Simple Search',
//    'title_or_trans' => 'Title',
    'year_from' => 'year (from)',
    'year_to' => 'year (to)',
];
