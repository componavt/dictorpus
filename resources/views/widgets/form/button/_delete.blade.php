<?php
        $args_for_route = [];
        if (isset($id)) {
            $args_for_route['id'] = $id;
        }
        if (isset($url_args)) {
            $args_for_route = array_merge($args_for_route,$url_args);
        }
//        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s" class="btn btn-default"><i class="fa fa-trash-o"></i></a>';
        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s"';
        if (isset($is_button) && $is_button) {
            $format .= ' class="btn btn-xs btn-danger"';
        }
        $format .= '><i class="fa fa-trash fa-lg"></i> %s</a>';
        $link = URL::route($route, $args_for_route);
                //LaravelLocalization::localizeURL($route);
        $token = csrf_token();
        if (isset($without_text) && $without_text) {
            $title = '';
        } else {
            $title = \Lang::get('messages.delete');
        }
        print sprintf($format, $link, $token, $title, $title);
