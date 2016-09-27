<?php
        $format = '<a  href="%s"';
        if (isset($is_button) && $is_button) {
            $format .= ' class="btn btn-warning btn-xs btn-detail"';
        }
        $format .= '><i class="fa fa-pencil fa-lg"></i> %s</a>';
        $link = LaravelLocalization::localizeURL($route);
        if (isset($without_text) && $without_text) {
            $title = '';
        } else {
            $title = \Lang::get('messages.edit');
        }
        print sprintf($format, $link, $title);
