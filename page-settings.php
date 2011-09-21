<?php
namespace wp2static\settings;
use wp2static;

require_once('lib/settings.lib.php');

function no_op() {}

const WINDOW_SIZE = 5;

//create settings when admin section is init-ed
add_action('admin_init', function() {
    //register our settings object
    register_setting(
        wp2static\NAME . '_options', //option group
        wp2static\NAME,              //option name
        function($options) {         //validator
            
            //ensure that window_size is always something useful
            if(!intval($options['window_size'])) {
                $options['window_size'] = WINDOW_SIZE;
            }
            
            return $options;
        }
    );
    
    //Settings Sections
    add_settings_section(
        'main',                             //section id
        'General',                          //section title
        'wp2static\settings\no_op',         //section sub-header        
        wp2static\SETTINGS                  //settings page to add this section to
    );
    
    add_settings_section(
        'misc', 
        'Miscellaneous',
        'wp2static\settings\no_op',
        wp2static\SETTINGS
    );
    
    //Main
    create(array(
        'id'        => 'storage',
        'title'     => 'Storage Method',
        'desc'      => 'Select the storage method used to save files',
        'type'      => 'select',
        'choices'   => array(
            'local' => 'Local Filesystem'
        )
    ));
    
    create(array(
        'id'    => 'base_loc',
        'title' => 'Base Location',
        'desc'  => 'Location for the files to be copied to (make sure the server can write to this location!)',
        'class' => 'large-text'
    ));
    
    //Misc
    create(array(
        'section'   => 'misc',
        'id'        => 'window_size',
        'title'     => 'Simultaneous Requests',
        'desc'      => 'Number of pages to request simultaneously while exporting. Experiment to determine optimal value.'
    ));
});

//add the debug output stuff last
add_action('admin_init', function() {
    add_settings_section(
        'debug', 
        'Debug',
        'wp2static\settings\no_op',
        wp2static\SETTINGS
    );
    
    create(array(
        'id'      => 'enable_logging',
        'title'   => 'Enable debug logging',
        'desc'    => 'Log verbosely to the PHP error log?',
        'section' => 'debug',
        'type'    => 'checkbox'
    ));
}, 100);

add_action('admin_menu', function() {
    add_options_page(
        'WP2Static Options',    //page title
        'WP2Static',            //menu item
        'manage_options',       //permissions
        wp2static\SETTINGS,     //slug
        function() {            //callback to render page contents
            include('templates/settings.tmpl');
        }
    );
});
