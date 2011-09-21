<?php

namespace wp2static;

use wp2static\iterators;

require_once('file-iterators.class.php');

function filter($args) {
    $defaults = array(
        'source' => '',
        'whitelist' => array(),
        'blacklist' => array()
    );
    
    $args = wp_parse_args($args, $defaults);
    
    $i = new iterators\FileExtensionFilterIterator(
            new iterators\FileFilterIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($args['source']), 
                    true
                )
            ),
            $args['whitelist'],
            $args['blacklist']
        );
    
    
    $files = array();
    
    foreach($i as $f) {
        $files[] = (string) $f;
    }
    
    return $files;
}
