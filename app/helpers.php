<?php

use Illuminate\Support\Str;

/**
 * Creates a revision record.
 *
 * @param object $obj
 * @param string $key
 * @param mixed $old
 * @param mixed $new
 *
 * @return bool
 */
function createRevisionRecord($obj, $key, $old = null, $new = null)
{
    if (gettype($obj) != 'object') {
        return false;
    }
    $revisions = [
        [
            'revisionable_type' => get_class($obj),
            'revisionable_id' => $obj->getKey(),
            'key' => $key,
            'old_value' => $old,
            'new_value' => $new,
            'user_id' => vms_user('id'),
            'created_at' => new \DateTime(),
            'updated_at' => new \DateTime(),
        ]
    ];
    $revision = new \Venturecraft\Revisionable\Revision;
    \DB::table($revision->getTable())->insert($revisions);
    return true;
}

if (! function_exists('number_with_space')) {
    function number_with_space($num) {
        return number_format($num, 0, '', ' ');
    }
}

if (! function_exists('show_route')) {
    function show_route($model, $args_by_get=null)
    {
        return route_for_model($model, 'show', $args_by_get);
    }
}

if (! function_exists('route_for_model')) {
    function route_for_model($model, $route, $args_by_get=null/*, $resource = null*/)
    {
/*        $resource = $resource ?? plural_from_model($model);

        return route("{$resource}.show", $model);*/
        return route(single_from_model($model).".{$route}", $model).$args_by_get;
    }
}
if (! function_exists('single_from_model')) {
    function single_from_model($model)
    {
        return snake_case(class_basename($model));
    }
}

if (! function_exists('plural_from_model')) {
    function plural_from_model($model)
    {
        $plural = Str::plural(class_basename($model));

        return Str::camel($plural);
    }
}

if (! function_exists('user_dict_edit')) {
    function user_dict_edit()
    {
        return User::checkAccess('dict.edit');
    }
}

if (! function_exists('user_dict_add')) {
    function user_dict_add()
    {
        return User::checkAccess('dict.add');
    }
}

if (! function_exists('user_corpus_edit')) {
    function user_corpus_edit()
    {
        return User::checkAccess('corpus.edit');
    }
}

if (! function_exists('user_photo_edit')) {
    function user_photo_edit()
    {
        return User::checkAccess('photo.edit');
    }
}

if (! function_exists('to_sql')) {
    function to_sql($query)
    {
//dd($query->toSql(), $query->getBindings());        
        return vsprintf(str_replace(array('?'), array('\'%s\''), $query->toSql()), $query->getBindings());            

    }
}

if (! function_exists('convert_quotes')) {
    function convert_quotes($str) {
        $chr_map = array(
           // Windows codepage 1252
//           "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
           "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
//           "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
//           "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
//           "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
           "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
           "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
//           "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark

           // Regular Unicode     // U+0022 quotation mark (")
                                  // U+0027 apostrophe     (')
           "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
           "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
//           "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
//           "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
//           "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
//           "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
           "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
           "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
           "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
           "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
//           "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
//           "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
        );
        $chr = array_keys  ($chr_map);
        $rpl = array_values($chr_map);
        return str_replace($chr, $rpl, html_entity_decode($str, ENT_QUOTES, "UTF-8"));
    }
}

if (! function_exists('to_link')) {
    function to_link($str, $link)
    {
        return '<a href="'.LaravelLocalization::localizeURL($link).'">'.$str.'</a>';            

    }
}

// extracts some parameters from object Request into array $url_args
if (! function_exists('url_args')) {
    function url_args($request, $limit_min=10) {
        $url_args = [
            'limit_num' => (int)$request->input('limit_num'), // number of records per page
            'page'      => (int)$request->input('page'),      // number of page
        ];
        if (!$url_args['page']) {
            $url_args['page'] = 1;
        }
        
        if ($url_args['limit_num']<=0) {
            $url_args['limit_num'] = $limit_min;
        } elseif ($url_args['limit_num']>1000) {
            $url_args['limit_num'] = 1000;
        }   
        return $url_args;
    }
}

// Converts the array $url_args (name->value) to String
// Usage: 
// $this->args_by_get = search_values_by_URL($this->url_args);
if (! function_exists('search_values_by_URL')) {
    function search_values_by_URL(array $url_args=NULL)
    {
        $out = http_build_query(remove_empty($url_args));
        return $out ? '?'.$out : '';
    }
}

if (! function_exists('remove_empty')) {
    function remove_empty(array $url_args=NULL)
    {
        if (isset($url_args['limit_num']) && $url_args['limit_num']==10) {
            unset($url_args['limit_num']);
        }
        if (isset($url_args['page']) && $url_args['page']==1) {
            unset($url_args['page']);
        }
        foreach ( $url_args as $k=>$v ) {
            if (!$v || is_array($v) && (!sizeof($v) || sizeof($v)==1 && isset($v[1]) && !$v[1])) {
                unset($url_args[$k]);
            } 
        }
        return $url_args;
    }
}

if (!function_exists('mb_ucfirst') && function_exists('mb_substr')) {
    function mb_ucfirst($string) {
        $string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
        return $string;
    }
}

if (!function_exists('mb_ucfirst') && function_exists('mb_substr')) {
    function mb_ucfirst($string) {
        $string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
        return $string;
    }
}

if (!function_exists('prev_args')) {
    function prev_args($url_args) {
        $url_args['page'] = $url_args['page'] > 1 ? $url_args['page']-1 : 1;
        return search_values_by_URL($url_args);
    }
}

if (!function_exists('next_args')) {
    function next_args($url_args) {
        $url_args['page'] = $url_args['page']+1;
        return search_values_by_URL($url_args);
    }
}

if (!function_exists('args_replace')) {
    function args_replace($url_args, $key, $value) {
        $url_args[$key] = $value;
        return search_values_by_URL($url_args);
    }
}

if (!function_exists('process_text')) {
    function process_text($text) {
        $text = str_replace("\n", "<br>", trim($text));
        return $text;
    }
}

if (!function_exists('count_not_empty_elems')) {
    function count_not_empty_elems($list) {
        $count = 0;
        foreach ($list as $key => $value) {
            if (is_array($value) && sizeof($value)>0 && !empty($value[0])) {
                $count++;
//print "<p>$key=$value[0]</p>";                
            } elseif(!empty($value)) {
                $count++;
//print "<p>$key=$value</p>";                
            }
        }
        return $count;
    }
}

if (!function_exists('remove_hyphens')) {
    function remove_hyphens($str) {
        return preg_replace('/&shy;/', '', $str);
    }
}

if (!function_exists('format_number')) {
    function format_number($total) {
        return number_format($total, 0, ',', ' ');
    }
}

if (!function_exists('found_rem')) {
/**
 * Высчитывается остаток от деления для последующего вычисления окончания фразы
 */
    function found_rem($total) {
        return $total>20 ? ($total%10==0 ? $total : $total%10)  : $total;
    }
}

if (!function_exists('highlight')) {
    function highlight($str, $substr, $class) {
        if (!$substr) {
            return $str;
        }
        return mb_ereg_replace('('.$substr.')', '<span class="'.$class.'">\\1</span>', $str, 'i');
    }
}
