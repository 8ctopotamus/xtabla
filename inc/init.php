<?php

$node_modules_path = plugin_dir_url( __DIR__ ) . 'node_modules/';

/*
** our plugin's uploads directory
*/
$upload_dir = wp_upload_dir();
$upload_dir = $upload_dir['basedir'];
$upload_dir = $upload_dir . '/xtabla-uploads';
define('XTABLA_UPLOADS_DIR', $upload_dir);

/*
** create xtabla-uploads directory in wp-content/uploads
*/
function xtabla_activate() { 
  if (! is_dir(XTABLA_UPLOADS_DIR)) {
     mkdir( XTABLA_UPLOADS_DIR, 0700 );
  }
}

/*
** Set up wp_ajax requests for frontend UI.
** NOTE: _nopriv_ makes ajaxurl work for logged out users.
*/
add_action( 'wp_ajax_xtabla_actions', 'xtabla_actions' );
// add_action( 'wp_ajax_nopriv_xtabla_actions', 'xtabla_actions' );
function xtabla_actions() {
  include( plugin_dir_path( __DIR__ ) . 'inc/actions.php' );
}

/*
 * Admin scripts and styles
 */
function xtabla_wp_admin_assets( $hook ) {
  global $node_modules_path;
  wp_register_style( 'xtabla_admin_styles', plugin_dir_url( __DIR__ ) . '/css/admin.css', false, '1.0.0' );
  wp_register_script('xtabla_admin_js', plugin_dir_url( __DIR__ ) . '/js/admin.js', array('jquery'), '', true );
  wp_register_script('jquery_jeditable', $node_modules_path . '/jquery-jeditable/dist/jquery.jeditable.min.js', array('jquery'), '', true );
  wp_register_script('xtabla_admin_editor_js', plugin_dir_url( __DIR__ ) . '/js/admin-editor.js', array('jquery'), '', true );
  // load on both admin views
  if ( $hook == 'toplevel_page_xtabla' || $hook == 'admin_page_xtabla-table-editor' ){
    wp_enqueue_style( 'xtabla_admin_styles' );
  }
  // main admin view
  if ( $hook === 'toplevel_page_xtabla' ) {
    add_thickbox(); // init Thickbox modal 
    wp_localize_script( 'xtabla_admin_js', 'wp_data', array( 
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'plugin_url' => plugin_dir_url( __DIR__ ),
    ) );
    wp_enqueue_script( 'xtabla_admin_js' );
  }
  // editor view
  if( $hook === 'admin_page_xtabla-table-editor' ) {
    wp_enqueue_script( 'jquery_jeditable' );
    wp_localize_script( 'xtabla_admin_editor_js', 'wp_data', array( 
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'plugin_url' => plugin_dir_url( __DIR__ ),
    ) );
    wp_enqueue_script( 'xtabla_admin_editor_js' );
  }

}
add_action( 'admin_enqueue_scripts', 'xtabla_wp_admin_assets' );


/*
 * Frontend scripts and styles
 */
function xtabla_scripts_and_styles() {
  global $post, $node_modules_path;

  // styles
  wp_register_style('basictable_css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.basictable/1.0.9/basictable.min.css');
  wp_register_style('xtabla_css', plugins_url('/css/style.css',  __DIR__ ));

  // scripts
  wp_register_script('basictable_js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.basictable/1.0.9/jquery.basictable.min.js', array('jquery'), '', true );
  wp_register_script('xtabla_frontend_js', plugin_dir_url( __DIR__ ) . '/js/frontend.js', array('jquery'), '', true );

  $shortcodePresent = has_shortcode( $post->post_content, 'xtabla');

  if ( $shortcodePresent ) {
    wp_enqueue_style('basictable_css');
    wp_enqueue_style('xtabla_css');
    wp_enqueue_script('basictable_js');
    wp_enqueue_script('xtabla_frontend_js');
  }
}
add_action('wp_enqueue_scripts', 'xtabla_scripts_and_styles');