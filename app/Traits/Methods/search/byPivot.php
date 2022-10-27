<?php namespace App\Traits\Methods\search;

trait byPivot
{
    public static function searchByPivot($objs, $model_name, $pivot_name, $pivots) {
        if (!sizeof($pivots)) {
            return $objs;
        }
        return $objs->whereIn('id',function($query) use ($model_name, $pivot_name, $pivots){
                    $query->select($model_name.'_id')
                    ->from($pivot_name."_".$model_name)
                    ->whereIn($pivot_name.'_id',$pivots);
                });
    }
}
