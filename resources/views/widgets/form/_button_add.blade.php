<?php
/*        if (isset($args_by_get)) {
            $route .= $args_by_get;
        } elseif (isset($url_args) && sizeof($url_args)) {
            $tmp=[];
            foreach ($url_args as $a=>$v) {
                if ($v!='') {
                    $tmp[] = "$a=$v";
                }
            }
            if (sizeof ($tmp)) {
                $route .= "?".implode('&',$tmp);
            }
        }*/
        $format = '<a  data-add="%s" class="btn btn-warning btn-xs btn-detail %s" title="%s"><i class="fa fa-plus fa-lg"></i></a>';
        print sprintf($format, $data_add, $class, $title);
