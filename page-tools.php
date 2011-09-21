<?php

namespace wp2static;
use wp2static\Exporter;

add_action('admin_menu', function() {
    //add link to tools menu
    $page = add_management_page(
        'WP2Static', 
        'Static Export', 
        'manage_options', 
        TOOLS, 
        function() {
            include('templates/tools.tmpl');
        }
    );
});

add_action('wp_ajax_' . NAME . '_export', function() {
    ob_end_flush();
    
    include('templates/tools.output.top.tmpl');
    
    //handle AJAX requests
    new Exporter();
    
    include('templates/tools.output.bot.tmpl');
    
    die();
});

//register client-side code for enqueueing later
add_action('admin_init', function() {
    wp_register_script(TOOLS, '/wp-content/plugins/' . NAME . '/js/tools.js', array('jquery'));
    wp_register_style(TOOLS, '/wp-content/plugins/' . NAME . '/css/tools.css');
});

//enqueue client side code in the proper places
add_action('admin_print_scripts-tools_page_' . TOOLS, function() { 
    wp_enqueue_script(TOOLS);
});
add_action('admin_print_styles-tools_page_' . TOOLS, function() {
    wp_enqueue_style(TOOLS);
});
