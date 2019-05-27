<?php

/**
 * top level menu
 */
function xtabla_options_page() {
  // add top level menu page
  add_menu_page('Xtabla Settings', 'Xtabla', 'manage_options', 'xtabla', 'xtabla_options_page_html', 'dashicons-media-spreadsheet');
}
/**
 * register our xtabla_options_page to the admin_menu action hook
 */
add_action('admin_menu', 'xtabla_options_page');

/**
 * top level menu:
 * callback functions
 */
function xtabla_options_page_html() {
  if (!current_user_can('manage_options')) {
    return;
  }
  // init Thickbox modal 
  add_thickbox();
?>
  <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <a href="#TB_inline?&width=600&height=550&inlineId=my-content-id" class="thickbox button-secondary">Subir tabla</a>
    <div id="my-content-id" style="display:none;">
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
                <button class="view-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>">           <span class="dashicons dashicons-welcome-view-site"><span>
                </button>
                <button class="delete-spreadsheet" data-spreadsheetid="<?php echo $sheet; ?>">         <span class="dashicons dashicons-trash"><span>
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
