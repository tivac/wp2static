<?php
/*
Plugin Name: WP2Static
Description: Export your Wordpress site as static HTML
Version: 1.0
Author: Patrick Cavit
Author URI: http://www.patcavit.com
Provides: wp2static
*/

namespace wp2static;

const NAME      = 'wp2static';
const SETTINGS  = 'wp2static-settings';
const TOOLS     = 'wp2static-tools';

require('exporter.class.php');
require('page-tools.php');
require('page-settings.php');
require('action-theme.php');
require('action-plugins.php');
require('action-uploads.php');
require('action-feed.php');
require('action-ant.php');
require('filter-content.php');
require('filter-name.php');

//add settings link on plugins page, before other links
add_filter('plugin_row_meta', function($links, $file) {
    $plugin = basename(dirname(__FILE__)) . '/' . basename(__FILE__);
    
    if($file == $plugin) {
        $links[] = '<a href="options-general.php?page=' . SETTINGS . '">' . __('Settings') . '</a>';
    }
    
    return $links;
}, 10, 2);
