<?php
namespace wp2static\actions\theme;

use wp2static;
use wp2static\storage;
use wp2static\settings;

require_once('lib/settings.lib.php');
require_once('lib/filter.lib.php');

const FIELD = 'export_theme';

//hook exporting action to save out the theme
add_action(wp2static\ACTION_PREFIX . 'exporting', function($exporter) {
    $options = get_option(wp2static\NAME);
    if(!$options[FIELD]) return;
    
    $storage = new storage\Local($options['base_loc']);
    
    $files = wp2static\filter(array(
        'source' => get_template_directory(),
        'blacklist' => array('php', 'pot', 'po', 'mo', 'tmpl')
    ));
    
    foreach($files as $f) {
        $storage->copy($f, str_replace(\ABSPATH, '', $f));
    }
});

//add our option to the 'misc' section
add_action('admin_init', function() {
    settings\create(array(
        'id'      => FIELD,
        'title'   => 'Copy theme?',
        'desc'    => 'Copy current theme resources when exporting',
        'section' => 'misc',
        'type'    => 'checkbox'
    ));
});
