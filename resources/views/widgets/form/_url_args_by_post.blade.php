<?php
        if (isset($url_args) && sizeof($url_args)) {
            foreach ($url_args as $a=>$v) {
                if ($v!='') {?>
<input type="hidden" name="{{$a}}" value="{{$v}}">
<?php           }
            }
        }
?>
