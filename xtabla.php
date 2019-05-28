<?php
/*
Plugin Name: Xtabla
Plugin URI: 
Description: Crear tablas de un archivo XLSX o CSV.
Version: 0.1.0
Author: Zylo, LLC & ACM
Author URI: https://zylo.dev
Text Domain: xtabla
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/*
** create xtabla-uploads directory in wp-content/uploads
*/
function xtabla_activate() { 
  $upload = wp_upload_dir();
  $upload_dir = $upload['basedir'];
  $upload_dir = $upload_dir . '/xtabla-uploads';
  if (! is_dir($upload_dir)) {
     mkdir( $upload_dir, 0700 );
  }
}
register_activation_hook( __FILE__, 'xtabla_activate' );

include 'inc/init.php';
include 'inc/functions.php';
include 'inc/admin.php';
include 'inc/shortcode.php';