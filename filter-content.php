<?php
namespace wp2static\filters\content;

use wp2static;
use wp2static\settings;

require_once('lib/settings.lib.php');

const FIELD = 'rewrite_urls';

//hook exporting action to save out the theme
add_filter(wp2static\ACTION_PREFIX . 'response_content', function($content) {
    $options = get_option(wp2static\NAME);
    $base = get_bloginfo('url');
    
    if($options[FIELD]) {
        $content = str_replace($base, $options[FIELD . '_replacement'], $content);
    }
    
    return $content;
});

//add options to the 'main' section
add_action('admin_init', function() {
    $base = get_bloginfo('url');
    
    settings\create(array(
        'id'      => FIELD,
        'title'   => 'Rewrite URLs?',
        'desc'    => 'Rewrite URLs within page content while saving',
        'section' => 'main',
        'type'    => 'checkbox'
    ));
    
    settings\create(array(
        'id'        => FIELD . '_replacement',
        'title'     => 'Replacement',
        'desc'      => "{$base} will be replaced with the contents of this field",
        'section'   => 'main',
        'class'     => 'large-text'
    ));
});
