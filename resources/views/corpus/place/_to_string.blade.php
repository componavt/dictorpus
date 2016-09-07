<?php
        $info=[];
        if ($place->name) {
            $info[0] = $place->name;
            if ($place->other_names()->count()) {
                $other_name = $place->other_names()->where('lang_id',$lang_id)->first();
                if ($other_name) {
                    $info[0] .= " (".$other_name->name.")";
                }
            }
        }
        
        if ($place->district) {
            $info[] = $place->district->name;
        }
        
        if ($place->region) {
            $info[] = $place->region->name;
        }
        
        print join(', ', $info);

