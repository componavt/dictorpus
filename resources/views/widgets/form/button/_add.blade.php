<?php
        $format = '<i data-add="%s" class="fa fa-plus fa-lg %s" title="%s" onClick="addExample(this, %s)"></i>';
        print sprintf($format, $data_add, $class, $title, $relevance);
