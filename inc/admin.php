<?php
/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */
/**
 * custom option and settings
 */
function xtabla_settings_init() {
  // register a new setting for "xtabla" page
  register_setting('xtabla', 'xtabla_options');
  // register a new section in the "xtabla" page
  add_settings_section('xtabla_section_developers', __('The Matrix has you.', 'xtabla'), 'xtabla_section_developers_cb', 'xtabla');
  // register a new field in the "xtabla_section_developers" section, inside the "xtabla" page
  add_settings_field('xtabla_field_pill', // as of WP 4.6 this value is used only internally
  // use $args' label_for to populate the id inside the callback
  __('Pill', 'xtabla'), 'xtabla_field_pill_cb', 'xtabla', 'xtabla_section_developers', ['label_for' => 'xtabla_field_pill', 'class' => 'xtabla_row', 'xtabla_custom_data' => 'custom', ]);
}
/**
 * register our xtabla_settings_init to the admin_init action hook
 */
add_action('admin_init', 'xtabla_settings_init');
/**
 * custom option and settings:
 * callback functions
 */
// developers section cb
// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function xtabla_section_developers_cb($args) {
?>
 <p id="<?php echo esc_attr($args['id']); ?>"><?php esc_html_e('Follow the white rabbit.', 'xtabla'); ?></p>
 <?php
}
// pill field cb
// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function xtabla_field_pill_cb($args) {
    // get the value of the setting we've registered with register_setting()
    $options = get_option('xtabla_options');
    // output the field
    
?>
 <select id="<?php echo esc_attr($args['label_for']); ?>"
 data-custom="<?php echo esc_attr($args['xtabla_custom_data']); ?>"
 name="xtabla_options[<?php echo esc_attr($args['label_for']); ?>]"
 >
 <option value="red" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'red', false)) : (''); ?>>
 <?php esc_html_e('red pill', 'xtabla'); ?>
 </option>
 <option value="blue" <?php echo isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], 'blue', false)) : (''); ?>>
 <?php esc_html_e('blue pill', 'xtabla'); ?>
 </option>
 </select>
 <p class="description">
 <?php esc_html_e('You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'xtabla'); ?>
 </p>
 <p class="description">
 <?php esc_html_e('You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'xtabla'); ?>
 </p>
 <?php
}
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
  // init Thickbox modal 
  add_thickbox();
  // check user capabilities
  if (!current_user_can('manage_options')) {
      return;
  }
  // add error/update messages
  // check if the user have submitted the settings
  // wordpress will add the "settings-updated" $_GET parameter to the url
  if (isset($_GET['settings-updated'])) {
      // add settings saved message with the class of "updated"
      add_settings_error('xtabla_messages', 'xtabla_message', __('Settings Saved', 'xtabla'), 'updated');
  }
  // show error/update messages
  settings_errors('xtabla_messages');
?>
  <div class="wrap">
  <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

  <a href="#TB_inline?&width=600&height=550&inlineId=my-content-id" class="thickbox button-secondary">Subir tabla</a>
  <div id="my-content-id" style="display:none;">
    <form action="options.php" method="post" enctype="multipart/form-data">
      Elige el archivo para subir
      <input type="file" name="fileToUpload" id="fileToUpload">
      <input type="submit" value="Subir" name="submit">
    </form>
  </div>

  <?php
    $spreadsheets = get_spreadsheets();
    if ($spreadsheets):
      echo '<div class="shortcode-details">';
      foreach($spreadsheets as $sheet):
        echo '<input value="[xtabla file=' . $sheet . ']" readonly type="text" />';
      endforeach;
      echo '</div>';
    else:
      echo 'No hay tablas creadas.';
    endif;
  ?>

 <!-- <form action="options.php" method="post">
  <?php
    // output security fields for the registered setting "xtabla"
    settings_fields('xtabla');
    // output setting sections and their fields
    // (sections are registered for "xtabla", each field is registered to a specific section)
    do_settings_sections('xtabla');
    // output save settings button
    submit_button('Save Settings');
  ?>
  </form> -->
  </div>
  <?php
}
