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
** Set up wp_ajax requests for frontend UI.
** NOTE: _nopriv_ makes ajaxurl work for logged out users.
*/
add_action( 'wp_ajax_xtabla_actions', 'xtabla_actions' );
// add_action( 'wp_ajax_nopriv_xtabla_actions', 'xtabla_actions' );
function xtabla_actions() {
  include( plugin_dir_path( __FILE__ ) . 'inc/actions.php' );
}

include 'inc/init.php';
include 'inc/functions.php';
include 'inc/admin.php';