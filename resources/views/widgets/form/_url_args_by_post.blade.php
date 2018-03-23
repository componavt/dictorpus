<?php
        if (isset($url_args) && sizeof($url_args)) {
            foreach ($url_args as $a=>$v) {
                if (is_array($v)) :
                    foreach ($v as $k=>$value) : ?>
<input type="hidden" name="{{$a}}[]" value="{{$value}}">
 <?php               endforeach;
                elseif ($v!='') :?>
<input type="hidden" name="{{$a}}" value="{{$v}}">
<?php           endif;
            }
        }
?>
