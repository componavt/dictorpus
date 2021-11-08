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

if (! function_exists('user_can_edit')) {
    function user_can_edit()
    {
        return User::checkAccess('dict.edit');
    }
}