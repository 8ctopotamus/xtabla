<?php
/*
** XTABLE_UPLOADS_DIR: our plugin's uploads directory
*/
$upload_dir = wp_upload_dir();
$upload_dir = $upload_dir['basedir'];
$upload_dir = $upload_dir . '/xtable-uploads';
define('XTABLE_UPLOADS_DIR', $upload_dir);

/*
** create xtable-uploads directory in wp-content/uploads
*/
if (! is_dir(XTABLE_UPLOADS_DIR)) {
  mkdir( XTABLE_UPLOADS_DIR, 0700 );
}

/*
** path to plugin's node_modules
*/
$node_modules_path = plugin_dir_url( __DIR__ ) . 'node_modules/';

$spreadsheetFileExtensions = ['.csv', '.xlsx', '.xls'];
$imageFileExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
$documentFileExtensions = ['.pdf', '.doc', '.docx', '.tif', '.tiff']; // note: they want .tif/.tiff files to display as download icon. We know it's really an imageFileExtension. :P

/*
** I18n
*/
add_action('plugins_loaded', 'xtable_load_textdomain');
function xtable_load_textdomain() {
	load_plugin_textdomain( 'xtable', false, dirname(plugin_basename(__DIR__)) . '/lang/' );
}

/*
** Set up wp_ajax requests for frontend UI.
** NOTE: _nopriv_ makes ajaxurl work for logged out users.
*/
add_action( 'wp_ajax_xtable_actions', 'xtable_actions' );
// add_action( 'wp_ajax_nopriv_xtable_actions', 'xtable_actions' );
function xtable_actions() {
  include( plugin_dir_path( __DIR__ ) . 'inc/actions.php' );
}

/*
 * Admin scripts and styles
 */
function xtable_wp_admin_assets( $hook ) {
  global $node_modules_path;

  // Style
  if (!wp_style_is( 'fontawesome', 'enqueued' )) {
    wp_register_style( 'fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css', false, '4.6.1' );
    wp_enqueue_style( 'fontawesome' );
  } 

  wp_register_style('animate_css', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css');
  wp_register_style('dropzone_css', $node_modules_path . '/dropzone/dist/dropzone.css');
  wp_register_style('xtable_admin_styles', plugin_dir_url( __DIR__ ) . '/css/admin.css', false, '1.0.0');

  // Script
  wp_register_script('dropzone_js', $node_modules_path . '/dropzone/dist/dropzone.js', '', '', true);
  wp_register_script('jquery_jeditable', $node_modules_path . '/jquery-jeditable/dist/jquery.jeditable.min.js', array('jquery'), '', true);
  wp_register_script('xtable_admin_js', plugin_dir_url( __DIR__ ) . '/js/admin.js', array('jquery'), '', true);
  wp_register_script('xtable_admin_editor_js', plugin_dir_url( __DIR__ ) . '/js/admin-editor.js', array('jquery'), '', true);

  $controlLabels = [
    'tooltip' => __('Click to edit', 'xtable'),
    'submit' => __('Save', 'xtable'),
    'cancel' => __('Cancel', 'xtable'),
    'mediaLibrary' => __('Media Library', 'xtable'),
  ];

  $localizationData = [ 
    'ajax_url' => admin_url( 'admin-ajax.php' ),
    'plugin_url' => plugin_dir_url( __DIR__ ),
    'control_labels' => $controlLabels,
  ];

  // main admin view
  if ( $hook === 'toplevel_page_xtable' ) {
    // codemirror
    $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
    wp_localize_script('jquery', 'cm_settings', $cm_settings);
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');

    add_thickbox();
    wp_enqueue_style( 'dropzone_css' );
    wp_enqueue_style( 'animate_css' );
    wp_enqueue_style( 'xtable_admin_styles' );
    wp_enqueue_script( 'dropzone_js' );
    wp_localize_script( 'xtable_admin_js', 'wp_data', $localizationData );
    wp_enqueue_script( 'xtable_admin_js' );
  }

  // editor view
  if( $hook === 'admin_page_xtable-table-editor' ) {
    wp_enqueue_media();
    wp_enqueue_style( 'xtable_admin_styles' );
    wp_enqueue_script( 'jquery_jeditable' );
    wp_localize_script( 'xtable_admin_editor_js', 'wp_data', $localizationData );
    wp_enqueue_script( 'xtable_admin_editor_js' );
  }
}
add_action( 'admin_enqueue_scripts', 'xtable_wp_admin_assets' );


/*
 * Frontend scripts and styles
 */
function xtable_scripts_and_styles() {
  global $post, $node_modules_path;

  if (!wp_style_is( 'fontawesome', 'enqueued' )) {
    wp_register_style( 'fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css', false, '4.6.1' );
    wp_enqueue_style( 'fontawesome' );
  } 

  // styles
  wp_register_style('basictable_css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.basictable/1.0.9/basictable.min.css');
  wp_register_style('xtable_css', plugins_url('/css/style.css',  __DIR__ ));

  // scripts
  wp_register_script('basictable_js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.basictable/1.0.9/jquery.basictable.min.js', array('jquery'), '', true );
  wp_register_script('xtable_frontend_js', plugin_dir_url( __DIR__ ) . '/js/frontend.js', array('jquery'), '', true );

  $shortcodePresent = has_shortcode( $post->post_content, 'xtable');

  if ( $shortcodePresent ) {
    wp_enqueue_style('basictable_css');
    wp_enqueue_style('xtable_css');
    wp_enqueue_script('basictable_js');
    wp_enqueue_script('xtable_frontend_js');
  }
}
add_action('wp_enqueue_scripts', 'xtable_scripts_and_styles');

/*
 * Output Custom CSS
 */
function xtable_custom_head() {
  $customCSS = get_option( 'xtable_design_settings' )['xtable_custom_css'];
  if ( $customCSS ):
    echo '<!-- Xtable Custom CSS -->';
    echo '<style>';
    echo wp_unslash( $customCSS );
    echo '</style>';
  endif;
}
add_action('wp_head', 'xtable_custom_head');