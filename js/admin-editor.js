(function($) {
  const { ajax_url: AJAX_URL } = wp_data
  const $cells = $('.xtabla-table td')

  $cells.editable("save.php");
})(jQuery)