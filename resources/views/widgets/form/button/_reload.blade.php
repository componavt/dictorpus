<?php
        $format = '<i data-reload="%s" class="fa fa-sync-alt fa-lg %s" title="%s" '.
                " onClick=\"%s(this, '%s')\"></i>";
        print sprintf($format, $data_reload, $class, $title, $func, LaravelLocalization::getCurrentLocale());
