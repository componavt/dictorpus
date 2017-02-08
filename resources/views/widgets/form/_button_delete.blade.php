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
//        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s" class="btn btn-default"><i class="fa fa-trash-o"></i></a>';
        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s"';
        if (isset($is_button) && $is_button) {
            $format .= ' class="btn btn-xs btn-danger"';
        }
        $format .= '><i class="fa fa-trash-o fa-lg"></i> %s</a>';
        $link = LaravelLocalization::localizeURL($route);
        $token = csrf_token();
        if (isset($without_text) && $without_text) {
            $title = '';
        } else {
            $title = \Lang::get('messages.delete');
        }
        print sprintf($format, $link, $token, $title, $title);
