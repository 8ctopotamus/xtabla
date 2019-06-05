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
  if (empty($_GET['sheet'])):
    $URL = admin_url() . 'admin.php?page=xtabla&error=No+sheet+provided';
    redirect($URL);
  endif;
  ?>
    <div class="wrap">
      <h1>Xtabla Spreadsheet Editor</h1>
      <?php if ( isset( $_GET['sheet'] ) ):
        echo renderSheets( $_GET['sheet'] );
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
      <form action="upload_file.php" method="post" enctype="multipart/form-data">
        <label for="file">Archivo:</label>
        <input type="file" name="file" id="file"><br>
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
          ?>
            <div id="sheet-<?php echo $filename; ?>" class="shortcodes-list-item <?php echo $extension; ?>">
              <div>
                <strong><?php echo $sheet; ?></strong>
              </div>
              <div>
                <code>[xtabla file="<?php echo $sheet; ?>"]</code>
              </div>
              <div>
                <a href="<?php echo admin_url() . 'admin.php?page=xtabla-table-editor&sheet=' . $sheet;?>" class="view-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>">
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
