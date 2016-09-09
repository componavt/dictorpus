<?php
//        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s" class="btn btn-default"><i class="fa fa-trash-o"></i></a>';
        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s"';
        if ($is_button) {
            $format .= ' class="btn btn-xs btn-danger"';
        }
        $format .= '>%s</a>';
        $link = URL::route($route, ['id' => $id]);
        $token = csrf_token();
        $title = \Lang::get('messages.delete');
        print sprintf($format, $link, $token, $title, $title);
