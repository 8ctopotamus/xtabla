(function($) {
  const { ajax_url } = wp_data
  const $cells = $('.xtabla-table td')
  const cssShadowVal = '0px 0px 2px green'

  let data = { 'action': 'xtabla_actions' }

  function updateCell(value, settings) {
    var $self = $(this)
    $self.css({ 'color': 'green', 'text-shadow': cssShadowVal,})
    data.value = value
    data.cellId = this.id
    data.do = 'update_spreadsheet'
    data.file = $(this).closest('table').data('spreadsheetid')
    $.post(ajax_url, data, function(response) {
      console.info('Response: ', response)
      $self.css({ 'color': 'inherit', 'text-shadow': 'none', })
    })
    return value
  }

  $cells.editable(updateCell);

})(jQuery)