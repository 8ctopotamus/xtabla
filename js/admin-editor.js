(function($) {
  const { ajax_url } = wp_data
  const $cells = $('.xtabla-table td')
  const $loading = $('#xtabla-loading')

  let params = { 'action': 'xtabla_actions' }

  const setLoading = bool => {
    if (bool) {
      $loading.show()
    } else {
      $loading.hide()
    }
  }

  function updateCell(value, settings) {
    setLoading(true)
    var $self = $(this)
    params.do = 'update_spreadsheet'
    params.file = $(this).closest('table').data('spreadsheetid')
    params.cellId = this.id
    params.value = value
    $.post(ajax_url, params, function(response) {
      $self.addClass('success')
      console.info('Response: ', response)
      setTimeout(() => $self.removeClass('success'), 1000)
    })
    .fail(function(err) {
      $self.addClass('failed')
      console.info('Error: ', err)
      setTimeout(() => $self.removeClass('failed'), 1000)
    })
    .done(() => setLoading(false))
    return value
  }

  $cells.editable(updateCell);

})(jQuery)