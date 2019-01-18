<?php
        $format = '<i data-for="%s" class="fa fa-times fa-lg %s" title="%s" onClick="removeExample(this)"></i>';
        print sprintf($format, $data_for, $class, $title);
