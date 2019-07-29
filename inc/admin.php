<?php

/**
 * add menu pages
 */
function xtable_options_page() {
  add_menu_page(
    'Xtable',
    'Xtable',
    'manage_options',
    'xtable',
    'xtable_options_page_html',
    'dashicons-media-spreadsheet'
  );

  add_submenu_page( 
    null,
    __('Xtable Editor', 'xtable'),
    __('Xtable Editor', 'xtable'),
    'manage_options',
    'xtable-table-editor',
    'xtable_submenu_page_callback'
  );

  // design tab settings
	add_settings_section(
		'xtable_xtableCustomCSS_section', 
		__( 'Custom styles', 'xtable' ), 
		'xtable_design_settings_section_callback', 
		'xtableCustomCSS'
	);
	add_settings_field( 
		'xtable_custom_css', 
		__( 'Custom CSS', 'xtable' ), 
		'xtable_design_settings_render', 
		'xtableCustomCSS',
		'xtable_xtableCustomCSS_section' 
	);
	register_setting( 'xtableCustomCSS', 'xtable_design_settings' );
}
add_action('admin_menu', 'xtable_options_page');



// design section heading
function xtable_design_settings_section_callback() {
  echo '<p>' . __('Customize the look of your tables on the frontend of your site. Click editor to enable.', 'xtable') . '</p>';
}
// design fields
function xtable_design_settings_render() { 
  $options = get_option( 'xtable_design_settings' ); 
  ?>
	  <textarea id="xtable-custom-css" name='xtable_design_settings[xtable_custom_css]' class="code-preview"><?php echo wp_unslash( $options['xtable_custom_css'] ); ?></textarea>
<?php	}

function xtable_submenu_page_callback() {
  $URL = admin_url() . 'admin.php?page=xtable';
  if (empty($_GET['sheet'])):
    redirect($URL . '&error=No+sheet+provided');
  endif;
  ?>
    <div class="wrap xtable-editor">
      <img class="xtable-logo" src="<?php echo plugins_url('/img/xtable-logo.svg',  __DIR__ ); ?>" alt="Xtable logo" />
    
      <?php 
      $sheet = $_GET['sheet'];
      if ( isset( $sheet ) ):
        echo '<p><a href="' . $URL . '"><< ' . __('Back to xtable dashboard', 'xtable') . '</a></p>';
        echo '<span id="xtable-loading" class="saving"><span>.</span><span>.</span><span>.</span></span>';
        echo '<button class="button-secondary add" data-add="row">+ ' . __('Row', 'xtable') . '</button>';
        echo '<button class="button-secondary add" data-add="column">+ ' . __('Column', 'xtable') . '</button>';
        echo '<button class="button-secondary delete" disabled>- ' . __('Delete Selected', 'xtable') . '</button>';
        echo '<h1>' . $sheet . '</h1>';
        echo '<div class="cell-label"></div>';
        echo renderSheets( $sheet );
      endif; ?>
    </div>
  <?php
}

/**
 * top level
 */
function xtable_options_page_html() {
  global $spreadsheetFileExtensions;

  if ( !current_user_can('manage_options') ) {
    return;
  }
  if ( isset($_GET['error']) ){
    echo '<p class="notice notice-error">' . $_GET['error'] . '</p>';
  }
?>
  <div class="wrap">
    
    <img class="xtable-logo" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/xtable-logo.svg'; ?>" alt="Xtable logo" />
    
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="tabs">
			<nav class="nav-tab-wrapper tab-list">
				<a class="tab nav-tab active" href="#spreadsheets-tab"><?php echo __('Spreadsheets', 'xtable'); ?></a>
				<a class="tab nav-tab"  href="#design-tab"><?php echo __('Design', 'xtable'); ?></a>
			</nav>
			<div id="spreadsheets-tab" class="tab-content show">
        
        <p><?php echo __('Manage your spreadsheets.', 'xtable'); ?></p>

        <a href="#TB_inline?&width=600&height=550&inlineId=file-upload-modal" class="thickbox button-secondary"><?php echo __('Upload spreadsheet', 'xtable') ?></a>

        <div id="file-upload-modal" style="display:none;">
          <form action="admin.php?page=xtable" method="post" enctype="multipart/form-data">
            <label for="file"><?php echo __('File:', 'xtable') ?></label><br/>
            <input 
              id="file" 
              name="file" 
              type="file"
              accept="<?php echo implode(',', $spreadsheetFileExtensions); ?>"
            />
            <br/>
            <input type="submit" name="submit" value="Submit" class="button-primary">
          </form> 
        </div>

        <h2><?php _e('Spreadsheets', 'xtable'); ?></h2>
        <div class="wrap-inner">
          <?php $spreadsheets = get_spreadsheets();
            echo '<div id="shortcodes-list">';
            if ($spreadsheets):
              foreach( $spreadsheets as $sheet ):
                $parts = explode('.', $sheet);
                $filename = $parts[0];
                $extension = $parts[1];
                $editURL = admin_url() . 'admin.php?page=xtable-table-editor&sheet=' . $sheet;
                $downloadURL = plugins_url( '/inc/download.php?sheet=' . $sheet . '&XTABLE_UPLOADS_DIR=' . XTABLE_UPLOADS_DIR,  __DIR__ );
              ?>
              <div id="sheet-<?php echo $filename; ?>" class="shortcodes-list-item <?php echo $extension; ?>">
                <div>
                  <a href="<?php echo $editURL ; ?>" class="view-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>">
                    <strong><?php echo $sheet; ?></strong>
                  </a>
                </div>
                <div>
                  <p class="copy-shortcode">[xtable file="<?php echo $sheet; ?>"]</p>
                </div>
                  <div class="text-right">
                    <a href="<?php echo $editURL ; ?>" class="view-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>" title="<?php _e('Edit', 'xtable'); ?>">
                      <button type="button"> 
                        <span class="dashicons dashicons-edit"></span>
                      </button>
                    </a>
                    <a href="<?php echo $downloadURL; ?>" class="download-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>" title="<?php _e('Download', 'xtable'); ?>" target="_blank">
                      <button type="button"> 
                        <span class="dashicons dashicons-download"></span>
                      </button>
                    </a>
                    <button class="delete-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>" title="<?php _e('Delete', 'xtable'); ?>"> 
                      <span class="dashicons dashicons-trash"></span>
                    </button>
                  </div>
                </div>
              <?php 
              endforeach;
            else:
              _e('No spreadsheets found.', 'xtable');
            endif;
            echo '</div>';
          ?>
        </div><!-- /.wrap-inner -->

      </div><!-- /.spreadsheets-tab -->
      <div id="design-tab" class="tab-content">
          
        <form method="post" action="options.php">
          <?php
            settings_fields( 'xtableCustomCSS' );
            do_settings_sections( 'xtableCustomCSS' );
            submit_button();
          ?>
        </form>

      </div><!-- /.design-tab -->
    </div><!-- /.tab -->

  </div>
  <?php
}
