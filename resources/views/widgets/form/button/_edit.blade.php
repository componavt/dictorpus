<?php
        if (isset($args_by_get)) {
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
        }
        $format = '<a  href="%s"';
        if (isset($is_button) && $is_button) {
            $format .= ' class="btn btn-warning btn-xs btn-detail"';
        } elseif (isset($link_class)) {
            $format .= ' class="'.$link_class.'"';
        }
        $format .= '><i class="fa fa-pencil-alt fa-lg"></i>%s</a>';
        $link = LaravelLocalization::localizeURL($route);
        if (isset($without_text) && $without_text) {
            $title = '';
        } else {
            $title = ' '.\Lang::get('messages.edit');
        }
        print sprintf($format, $link, $title);
