<?php

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