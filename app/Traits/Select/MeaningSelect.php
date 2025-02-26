<?php namespace App\Traits\Select;

use \Venturecraft\Revisionable\Revision;

use App\Models\User;

trait MeaningSelect
{
    public static function lastCreated($limit='') {
        $meanings = self::latest();
        if ($limit) {
            $meanings = $meanings->take($limit);
        }
        $meanings = $meanings->get();
        foreach ($meanings as $meaning) {
            $revision = Revision::where('revisionable_type','like','%Meaning')
                                ->where('key','created_at')
                                ->where('revisionable_id',$meaning->id)
                                ->latest()->first();
            if ($revision) {
                $meaning->user = User::getNameByID($revision->user_id);
            }
        }
        return $meanings;
    }
    
}