<?php
/**
 * @package Earthquake Contact Form
 */
/*
Plugin Name: Earthquake Contact Form
Plugin URI: 
Description: Thsi plugin is used to display a form and then store form date to database and show data listing in <strong>WP Dashboard</strong> with <strong>Export Feature</strong>
Version: 1.0.0
Author: Saddam Shahzad
Author URI: 
Text Domain: earthquake contact form
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
 

//installation and activation file
include(plugin_dir_path( __FILE__ ) .'ss-earthquake-contact-form-install.php');

// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'ss_earthquake_form_install');
register_deactivation_hook(__FILE__, 'ss_earthquake_form_uninstall'); 
register_uninstall_hook(__FILE__, 'ss_earthquake_form_uninstall' );


//register custom plugin menu page
add_action( 'admin_menu', 'register_custom_plugin_menu_page' );

function register_custom_plugin_menu_page(){
    add_menu_page( 'custom menu title', 'Web Form', 'manage_options', 'ss-earthquake-contact-form/ss-earthquake-contact-form-admin.php', '', plugins_url( 'ss-earthquake-contact-form/images/icon-logo.png' ), 6 );
    add_submenu_page( 'custom menu title 2', 'Page Title', 'Menu Title', 'administrator', 'ss-earthquake-contact-form/excel-import.php');
}



function contactFormCode() {
    // turn on output buffering to capture script output
    ob_start();
    include(plugin_dir_path( __FILE__ ) .'contact_form.php');

    $content = ob_get_clean();
    return $content;
}
add_shortcode('EarthContactForm', 'contactFormCode');