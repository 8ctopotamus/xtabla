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
    'xtabla_submenu_page_callback',
  );
}
add_action('admin_menu', 'xtabla_options_page');

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
        echo '<h1>';
        echo $sheet;
        echo '</h1>';
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

    <!-- <h2>Upload Spreadsheet</h2>
    <form method="POST" enctype="multipart/form-data" action="admin.php?action=xtabla_actions&do=upload_spreadsheet" class="dropzone">
      <div class="fallback">
	     <input name="file" type="file" multiple /> 
      </div>
      <input type="submit" name="submit" value="Submit">
    </form>
    <p class="max-upload-size"><?php printf( __( 'Maximum upload file size: %s.' ), esc_html( size_format( wp_max_upload_size() ) ) ); ?></p> -->

    <a href="#TB_inline?&width=600&height=550&inlineId=file-upload-modal" class="thickbox button-secondary">Subir tabla</a>
    <div id="file-upload-modal" style="display:none;">
      <form action="admin.php?page=xtabla" method="post" enctype="multipart/form-data">
        <label for="file">Archivo:</label>
        <input type="file" name="file" id="file"><br/>
        <input type="submit" name="submit" value="Submit">
      </form> 
    </div>

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
                <!-- <p class="copy-shortcode">fffffffffffff</p> -->
                <p class="copy-shortcode">[xtabla file="<?php echo $sheet; ?>"]</p>
              </div>
              <div>
                <a href="<?php echo $editLink ; ?>" class="view-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>">
                  <span class="dashicons dashicons-welcome-view-site"><span>
                </a>
                <button class="delete-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>"> 
                  <span class="dashicons dashicons-trash"><span>
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
    </div>
  </div>
  <?php
}
