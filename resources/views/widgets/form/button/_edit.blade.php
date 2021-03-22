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
        if (isset($without_text) && $without_text) {
            $link_text = '';
        } else {
            $link_text = ' '.\Lang::get('messages.edit');
        }
        $link = LaravelLocalization::localizeURL($route);
        $format = '<a  href="%s"';
        if (isset($is_button) && $is_button) {
            $format .= ' class="btn btn-warning btn-xs btn-detail"';
        } elseif (isset($link_class)) {
            $format .= ' class="'.$link_class.'"';
        }
        $format .= '><i class="fa fa-pencil-alt fa-lg" title="%s"></i>%s</a>';
        print sprintf($format, $link, $title ?? '', $link_text);
