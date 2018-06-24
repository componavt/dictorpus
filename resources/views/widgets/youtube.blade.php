<?php
    if ($video) :
        if (!isset($width)) {
            $width = 640;
        }
        if (!isset($height)) {
            $height = 390;
        }
    
        $url = "https://www.youtube.com/embed/$video?rel=0";
        if (isset($start) && $start) {
            $url .= "&start=$start";
        }
?>        
<iframe width="<?=$width?>" height="<?=$height?>" src="<?=$url?>" frameborder="0" allowfullscreen wmode="transparent"></iframe>
<?php
    endif;
?>
