<?php

/**
 * Plugin Name: ABHA Card Create
 * Version: 1.1
 * Author: Dhrumil 
 * Text Domain: abha-card-create  
 */

define('MD_URL', plugins_url('/', __FILE__));
define('MD_PATH', plugin_dir_path(__FILE__));

add_action('wp_enqueue_scripts', 'MD_init');
function MD_init()
{
    wp_enqueue_style('abha-card-create', MD_URL . 'css/componant.css', [], rand());

    wp_enqueue_script('abha-card-create', MD_URL . 'js/componant.js', array('jquery'), rand());
}
