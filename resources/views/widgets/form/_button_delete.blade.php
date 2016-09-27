<?php
//        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s" class="btn btn-default"><i class="fa fa-trash-o"></i></a>';
        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s"';
        if (isset($is_button) && $is_button) {
            $format .= ' class="btn btn-xs btn-danger"';
        }
        $format .= '><i class="fa fa-trash-o fa-lg"></i> %s</a>';
        $link = URL::route($route, ['id' => $id]);
        $token = csrf_token();
        if (isset($without_text) && $without_text) {
            $title = '';
        } else {
            $title = \Lang::get('messages.delete');
        }
        print sprintf($format, $link, $token, $title, $title);
