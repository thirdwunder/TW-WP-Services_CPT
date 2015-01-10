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
$prefix = 'tw_';

$service_slug = get_option('wpt_tw_service_slug') ? get_option('wpt_tw_service_slug') : "service";
$service_search = get_option('wpt_tw_service_search') ? true : false;
$service_archive = get_option('wpt_tw_service_archive') ? true : false;

$service_category = (get_option('wpt_tw_service_category')=='on') ? true : false;
$project_tag      = (get_option('wpt_tw_service_tag')=='on') ? true : false;

$service_testimonials  = (get_option('wpt_tw_service_testimonials')=='on') ? true : false;
$service_clients       = (get_option('wpt_tw_service_clients')=='on')      ? true : false;
$service_projects      = (get_option('wpt_tw_service_projects')=='on')     ? true : false;

TW_Services_Plugin()->register_post_type(
                        'tw_service',
                        __( 'Services',     'tw-services-plugin' ),
                        __( 'Service',      'tw-services-plugin' ),
                        __( 'Services CPT', 'tw-services-plugin'),
                        array(
                          'menu_icon'=>plugins_url( 'assets/img/cpt-icon-service.png', __FILE__ ),
                          'rewrite' => array('slug' => $service_slug),
                          'exclude_from_search' => $service_search,
                          'has_archive'     => $service_archive,
                        )
                    );
if($project_category){
  TW_Services_Plugin()->register_taxonomy( 'tw_service_category', __( 'Service Categories', 'tw-services-plugin' ), __( 'Service Category', 'tw' ), 'tw_service', array('hierarchical'=>true) );
}

if($project_tag){
 TW_Services_Plugin()->register_taxonomy( 'tw_service_tag', __( 'Service Tags', 'tw-services-plugin' ), __( 'Service Tag', 'tw-services-plugin' ), 'tw_service', array('hierarchical'=>false) );
}

if (is_admin()){
  $service_config = array(
    'id'             => 'tw_service_cpt_metabox',
    'title'          => 'Service Details',
    'pages'          => array('tw_service'),
    'context'        => 'normal',
    'priority'       => 'high',
    'fields'         => array(),
    'local_images'   => true,
    'use_with_theme' => false
  );
  $service_meta =  new AT_Meta_Box($service_config);

  $service_meta->addText('tw_service_url',array('name'=> 'Service URL', 'desc'=>'Service Website URL. External links must include http://'));

  if(is_plugin_active('tw-clients-plugin/tw-clients-plugin.php') && $service_clients){
    $service_meta->addPosts('tw_service_clients',array('post_type' => 'tw_client','type'=>'checkbox_list'),array('name'=> 'Clients'));
  }

  if( is_plugin_active( 'tw-testimonials-plugin/tw-testimonials-plugin.php' ) && $service_testimonials ){
    $service_meta->addPosts('tw_service_testimonials',array('post_type' => 'tw_testimonial', 'type'=>'checkbox_list'),array('name'=> 'Testimonials'));
  }

  if( is_plugin_active( 'tw-projects-plugin/tw-projects-plugin.php' ) && $service_projects ){
    $service_meta->addPosts('tw_service_projects',array('post_type' => 'tw_project', 'type'=>'checkbox_list'),array('name'=> 'Projects'));
  }


  $service_meta->Finish();

}