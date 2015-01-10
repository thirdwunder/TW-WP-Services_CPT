<?php
/*
 * Plugin Name: Third Wunder Services Plugin
 * Version: 1.0
 * Plugin URI: http://www.thirdwunder.com/
 * Description: Third Wunder services CPT plugin
 * Author: Mohamed Hamad
 * Author URI: http://www.thirdwunder.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: tw-services-plugin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Mohamed Hamad
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Load plugin class files
require_once( 'includes/class-tw-services-plugin.php' );
require_once( 'includes/class-tw-services-plugin-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-tw-services-plugin-admin-api.php' );
require_once( 'includes/lib/class-tw-services-plugin-post-type.php' );
require_once( 'includes/lib/class-tw-services-plugin-taxonomy.php' );

if(!class_exists('AT_Meta_Box')){
  require_once("includes/My-Meta-Box/meta-box-class/my-meta-box-class.php");
}

/**
 * Returns the main instance of TW_Services_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object TW_Services_Plugin
 */
function TW_Services_Plugin () {
	$instance = TW_Services_Plugin::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = TW_Services_Plugin_Settings::instance( $instance );
	}

	return $instance;
}

TW_Services_Plugin();
