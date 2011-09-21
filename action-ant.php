<?php
namespace wp2static\actions\ant;

use wp2static;
use wp2static\settings;

require_once('lib/settings.lib.php');

const FIELD = 'run_ant';

//hook exporting action to potentially run ant build
//added at priority 1 to attempt to ensure it runs first
add_action(wp2static\ACTION_PREFIX . 'exporting', function($exporter) {
    $options = get_option(wp2static\NAME);
    if(!$options[FIELD . '_before']) return;
    
    $output = array();
    
    exec('ant -f ' . $options[FIELD . '_before_file'], $output);
}, 1);

//hook done exporting action to potentially run ant build
//added at priority 100 to allow anything else to finish
add_action(wp2static\ACTION_PREFIX . 'done_exporting', function($exporter) {
    $options = get_option(wp2static\NAME);
    if(!$options[FIELD . '_after']) return;
    
    $output = array();
    
    exec('ant -f ' . $options[FIELD . '_after_file'], $output);
}, 100);

//add options to the 'misc' section
add_action('admin_init', function() {
    settings\create(array(
        'id'      => FIELD . '_before',
        'title'   => 'Run Ant before exporting?',
        'desc'    => 'Execute an Ant build file before exporting',
        'section' => 'misc',
        'type'    => 'checkbox'
    ));
    
    settings\create(array(
        'id'        => FIELD . '_before_file',
        'title'     => 'Ant file',
        'desc'      => 'Ant file to execute',
        'section'   => 'misc',
        'class'     => 'large-text'
    ));
    
    settings\create(array(
        'id'      => FIELD . '_after',
        'title'   => 'Run Ant after exporting?',
        'desc'    => 'Execute an Ant build file after exporting',
        'section' => 'misc',
        'type'    => 'checkbox'
    ));
    
    settings\create(array(
        'id'        => FIELD . '_after_file',
        'title'     => 'Ant file',
        'desc'      => 'Ant file to execute',
        'section'   => 'misc',
        'class'     => 'large-text'
    ));
});
