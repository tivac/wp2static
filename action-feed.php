<?php
namespace wp2static\actions\feed;

use wp2static;
use wp2static\settings;

require_once('lib/settings.lib.php');

const FIELD = 'save_feed';

//hook exporting action to save out the theme
add_action(wp2static\ACTION_PREFIX . 'exporting', function($exporter) {
    $options = get_option(wp2static\NAME);
    if(!$options[FIELD]) return;
    
    $exporter->addUrl(get_bloginfo('rss2_url'));
});

//add our option to the 'misc' section
add_action('admin_init', function() {
    settings\create(array(
        'id'      => FIELD,
        'title'   => 'Save Feed?',
        'desc'    => 'Save a copy of the feed for this site',
        'section' => 'misc',
        'type'    => 'checkbox'
    ));
});
