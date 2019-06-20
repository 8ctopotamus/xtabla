<?php

/**
 * add menu pages
 */
function xtabla_options_page() {
  add_menu_page(
    'Xtabla Settings',
    'Xtabla',
    'manage_options',
    'xtabla',
    'xtabla_options_page_html',
    'dashicons-media-spreadsheet'
  );

  add_submenu_page( 
    null,
    'Xtabla Table Editor',
    'Xtabla Table Editor',
    'manage_options',
    'xtabla-table-editor',
    'xtabla_submenu_page_callback'
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
add_action('admin_menu', 'xtabla_options_page');



// design section heading
function xtable_design_settings_section_callback() {
	echo 'Click to enable editor.';
}
// design fields
function xtable_design_settings_render() { 
  $options = get_option( 'xtable_design_settings' ); 
  ?>
	  <textarea id="xtable-custom-css" name='xtable_design_settings[xtable_custom_css]' class="code-preview"><?php echo wp_unslash( $options['xtable_custom_css'] ); ?></textarea>
<?php	}

function xtabla_submenu_page_callback() {
  $URL = admin_url() . 'admin.php?page=xtabla';
  if (empty($_GET['sheet'])):
    redirect($URL . '&error=No+sheet+provided');
  endif;
  ?>
    <div class="wrap xtabla-editor">
      <?php 
      $sheet = $_GET['sheet'];
      if ( isset( $sheet ) ):
        echo '<p><a href="' . $URL . '"><< Back to Xtabla dashboard</a></p>';
        echo '<span id="xtabla-loading" class="saving"><span>.</span><span>.</span><span>.</span></span>';
        // echo '<button class="add-row">+ Add Row</button>';
        // echo '<button class="add-column">+ Add Column</button>';
        echo '<h1>' . $sheet . '</h1>';
        // echo '<a href="' . XTABLA_UPLOADS_DIR . '/' . $sheet .'" download>Download Spreadsheet</a>';
        echo '<div class="cell-label"></div>';
        // echo '<button class="add-row add-row-top">&plus;</button>';
        // echo '<button class="add-row add-row-bottom">&plus;</button>';
        // echo '<button class="add-col add-col-left">&plus;</button>';
        // echo '<button class="add-col add-col-right">&plus;</button>';
        echo renderSheets( $sheet );
      endif; ?>
    </div>
  <?php
}

/**
 * top level
 */
function xtabla_options_page_html() {
  if ( !current_user_can('manage_options') ) {
    return;
  }
  if ( isset($_GET['error']) ){
    echo '<p class="notice notice-error">' . $_GET['error'] . '</p>';
  }
?>
  <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <a href="#TB_inline?&width=600&height=550&inlineId=file-upload-modal" class="thickbox button-secondary">Subir tabla</a>
    <div id="file-upload-modal" style="display:none;">
      <form action="admin.php?page=xtabla" method="post" enctype="multipart/form-data">
        <label for="file">Archivo:</label><br/>
        <input type="file" name="file" id="file" accept=".csv,.xlsx"><br/>
        <input type="submit" name="submit" value="Submit" class="button-primary">
      </form> 
    </div>

    <h2>Spreadsheets</h2>
    <div class="wrap-inner">
      <?php $spreadsheets = get_spreadsheets();
        echo '<div id="shortcodes-list">';
        if ($spreadsheets):
          foreach( $spreadsheets as $sheet ):
            $parts = explode('.', $sheet);
            $filename = $parts[0];
            $extension = $parts[1];
            $editLink = admin_url() . 'admin.php?page=xtabla-table-editor&sheet=' . $sheet;
          ?>
          <div id="sheet-<?php echo $filename; ?>" class="shortcodes-list-item <?php echo $extension; ?>">
            <div>
              <a href="<?php echo $editLink ; ?>" class="view-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>">
                <strong><?php echo $sheet; ?></strong>
              </a>
            </div>
            <div>
              <p class="copy-shortcode">[xtabla file="<?php echo $sheet; ?>"]</p>
            </div>
              <div class="text-right">
                <a href="<?php echo $editLink ; ?>" class="view-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>">
                  <button type="button"> 
                    <span class="dashicons dashicons-edit"></span>
                  </button>
                </a>
                <button class="delete-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>"> 
                  <span class="dashicons dashicons-trash"></span>
                </button>
              </div>
            </div>
          <?php 
          endforeach;
        else:
          echo 'No hay tablas creadas.';
        endif;
        echo '</div>';
      ?>
    </div><!-- /.wrap-inner -->

    <form method="post" action="options.php">
      <?php
        settings_fields( 'xtableCustomCSS' );
        do_settings_sections( 'xtableCustomCSS' );
        submit_button();
      ?>
    </form>
  </div>
  <?php
}
