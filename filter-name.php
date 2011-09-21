<?php
namespace wp2static\filters\name;

use wp2static;

//hook exporting action to name files based on content-type
add_filter(wp2static\ACTION_PREFIX . 'response_name', function($name, $info) {
    $ext = 'index.' . ((stristr($info['content_type'], 'xml')) ? 'xml' : 'html');

    return trailingslashit($name) . $ext;
}, 10, 2);
