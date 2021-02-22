<?php

defined('MOODLE_INTERNAL') || die;

function local_helloworld_extend_navigation_frontpage(navigation_node $parentnode){
    if(get_config('local_helloworld', 'showinnavigation') == 1 && isloggedin() && !isguestuser()){//need to move as this is only called once
        $parentnode->add(
            get_string('pluginname', 'local_helloworld'), 
            new moodle_url('/local/helloworld/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            null,
            new pix_icon('i/filter', ''));
    }
}
